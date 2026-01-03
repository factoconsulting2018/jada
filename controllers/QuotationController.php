<?php

namespace app\controllers;

use Yii;
use app\models\Quotation;
use app\models\QuotationProduct;
use app\models\Product;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\helpers\FileHelper;

/**
 * QuotationController handles the quotation cart functionality.
 */
class QuotationController extends Controller
{
    /**
     * Display quotation cart page
     * @return string
     */
    public function actionIndex()
    {
        $cart = Yii::$app->session->get('quotation_cart', []);
        $products = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::findOne($productId);
            if ($product && $product->status == Product::STATUS_ACTIVE) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return $this->render('index', [
            'products' => $products,
            'total' => $total,
        ]);
    }

    /**
     * Add product to cart (AJAX)
     * @return Response
     */
    public function actionAddToCart()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $productId = Yii::$app->request->post('product_id');
        $quantity = (int)Yii::$app->request->post('quantity', 1);
        
        if (!$productId) {
            return ['success' => false, 'message' => 'Producto no especificado.'];
        }

        $product = Product::findOne($productId);
        if (!$product || $product->status != Product::STATUS_ACTIVE) {
            return ['success' => false, 'message' => 'Producto no encontrado o no disponible.'];
        }

        $cart = Yii::$app->session->get('quotation_cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        
        Yii::$app->session->set('quotation_cart', $cart);
        
        $cartCount = array_sum($cart);
        
        return [
            'success' => true,
            'message' => 'Producto agregado al carrito.',
            'cart_count' => $cartCount,
        ];
    }

    /**
     * Remove product from cart (AJAX)
     * @return Response
     */
    public function actionRemoveFromCart()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $productId = Yii::$app->request->post('product_id');
        
        if (!$productId) {
            return ['success' => false, 'message' => 'Producto no especificado.'];
        }

        $cart = Yii::$app->session->get('quotation_cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Yii::$app->session->set('quotation_cart', $cart);
        }
        
        $cartCount = array_sum($cart);
        
        return [
            'success' => true,
            'message' => 'Producto eliminado del carrito.',
            'cart_count' => $cartCount,
        ];
    }

    /**
     * Update product quantity in cart (AJAX)
     * @return Response
     */
    public function actionUpdateQuantity()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $productId = Yii::$app->request->post('product_id');
        $quantity = (int)Yii::$app->request->post('quantity', 1);
        
        if (!$productId || $quantity < 1) {
            return ['success' => false, 'message' => 'Cantidad inválida.'];
        }

        $cart = Yii::$app->session->get('quotation_cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId] = $quantity;
            Yii::$app->session->set('quotation_cart', $cart);
        }
        
        $product = Product::findOne($productId);
        $subtotal = $product ? $product->price * $quantity : 0;
        
        // Recalculate total
        $total = 0;
        foreach ($cart as $pid => $qty) {
            $p = Product::findOne($pid);
            if ($p) {
                $total += $p->price * $qty;
            }
        }
        
        return [
            'success' => true,
            'subtotal' => number_format($subtotal, 2, '.', ','),
            'total' => number_format($total, 2, '.', ','),
        ];
    }

    /**
     * Search products for cart (AJAX)
     * @return Response
     */
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Yii::$app->request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $products = Product::find()
                ->where(['status' => Product::STATUS_ACTIVE])
                ->andWhere(['like', 'name', $query])
                ->limit(10)
                ->all();

            foreach ($products as $product) {
                $results[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->formattedPrice,
                    'image' => $product->imageUrl,
                ];
            }
        }

        return $results;
    }

    /**
     * Submit quotation form
     * @return Response|string
     */
    public function actionSubmit()
    {
        $cart = Yii::$app->session->get('quotation_cart', []);
        
        if (empty($cart)) {
            Yii::$app->session->setFlash('error', 'El carrito está vacío.');
            return $this->redirect(['index']);
        }

        $model = new Quotation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Add products to quotation
            foreach ($cart as $productId => $quantity) {
                $product = Product::findOne($productId);
                if ($product) {
                    $qp = new QuotationProduct();
                    $qp->quotation_id = $model->id;
                    $qp->product_id = $productId;
                    $qp->quantity = $quantity;
                    $qp->price = $product->price; // Store price at time of quotation
                    $qp->save();
                }
            }

            // Send email to client
            $this->sendQuotationEmailToClient($model);

            // Send email to admin
            $this->sendQuotationEmailToAdmin($model);

            // Store quotation data in session for success modal
            $quotationProducts = $model->quotationProducts;
            Yii::$app->session->set('quotation_success_data', [
                'quotation_id' => $model->id,
                'full_name' => $model->full_name,
                'email' => $model->email,
                'whatsapp' => $model->whatsapp,
                'products' => array_map(function($qp) {
                    return [
                        'product_name' => $qp->product->name,
                        'quantity' => $qp->quantity,
                        'price' => $qp->price,
                        'subtotal' => $qp->getSubtotal(),
                    ];
                }, $quotationProducts),
                'total' => $model->getTotal(),
            ]);

            // Clear cart
            Yii::$app->session->remove('quotation_cart');

            // Redirect to index to show modal
            return $this->redirect(['index']);
        }

        // If form not submitted or validation failed, show form
        $products = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::findOne($productId);
            if ($product && $product->status == Product::STATUS_ACTIVE) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return $this->render('submit', [
            'model' => $model,
            'products' => $products,
            'total' => $total,
        ]);
    }

    /**
     * Send quotation email to client
     */
    protected function sendQuotationEmailToClient($quotation)
    {
        try {
            $htmlBody = Yii::$app->view->renderFile('@app/views/mail/quotation-client.php', [
                'quotation' => $quotation,
            ]);
            
            $message = Yii::$app->mailer->compose()
                ->setTo($quotation->email)
                ->setFrom([Yii::$app->params['supportEmail'] => 'Tienda Online'])
                ->setSubject('Cotización #' . $quotation->id . ' - Tienda Online')
                ->setHtmlBody($htmlBody);
            
            $message->send();
        } catch (\Exception $e) {
            Yii::error('Error sending quotation email to client: ' . $e->getMessage());
        }
    }

    /**
     * Send quotation email to admin
     */
    protected function sendQuotationEmailToAdmin($quotation)
    {
        try {
            $adminEmail = Yii::$app->params['adminEmail'];
            
            $htmlBody = Yii::$app->view->renderFile('@app/views/mail/quotation-admin.php', [
                'quotation' => $quotation,
            ]);
            
            $message = Yii::$app->mailer->compose()
                ->setTo($adminEmail)
                ->setFrom([Yii::$app->params['supportEmail'] => 'Tienda Online'])
                ->setSubject('Nueva Cotización #' . $quotation->id . ' - ' . $quotation->full_name)
                ->setHtmlBody($htmlBody);
            
            $message->send();
        } catch (\Exception $e) {
            Yii::error('Error sending quotation email to admin: ' . $e->getMessage());
        }
    }

    /**
     * Get cart count (AJAX)
     * @return Response
     */
    public function actionGetCartCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $cart = Yii::$app->session->get('quotation_cart', []);
        $count = array_sum($cart);
        
        return ['count' => $count];
    }
}

