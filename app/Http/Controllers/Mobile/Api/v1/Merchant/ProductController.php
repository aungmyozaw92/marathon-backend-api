<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\Product;
use App\Models\Attachment;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\AttachmentRepository;
use App\Http\Resources\MerchantDashboard\Product\ProductResource;
use App\Http\Resources\MerchantDashboard\Product\ProductCollection;
use App\Repositories\Web\Api\v1\MerchantDashboard\ProductRepository;
use App\Http\Requests\MerchantDashboard\Product\CreateProductRequest;
use App\Http\Requests\MerchantDashboard\Product\UpdateProductRequest;
use App\Http\Requests\MerchantDashboard\Product\UploadProductRequest;

class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ProductController constructor.
     *
     * @param ProductRepository $ProductRepository
     */
    public function __construct(ProductRepository $productRepository, AttachmentRepository $attachmentRepository)
    {
        $this->middleware('can:view,product')->only('show');
        $this->middleware('can:update,product')->only('update');
        $this->middleware('can:update,product')->only('upload');
        $this->middleware('can:delete,product')->only('delete_file');
        $this->middleware('can:delete,product')->only('destroy');
        $this->productRepository = $productRepository;
        $this->attachmentRepository = $attachmentRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('attachments','product_type','inventory')->where('merchant_id', auth()->user()->id)
                        ->orderBy('id', 'desc');
        if (request()->has(['paginate'])) {
            $products = $products->paginate(request()->get('paginate'));
        }else{
            $products = $products->get();
        }
        return new ProductCollection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        $product =$this->productRepository->create($request->all());

        return new ProductResource($product->load(['inventory','attachments','product_type']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product->load(['inventory','attachments','product_type']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product =$this->productRepository->update($product, $request->all());

        return new ProductResource($product->load(['inventory','attachments','product_type']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $this->productRepository->destroy($product);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }

    public function upload(UploadProductRequest $request,Product $product)
    {
        $this->attachmentRepository->create_attachment($product, $request->all());

        return new ProductResource($product->load(['inventory','attachments','product_type']));
    }

    public function delete_file(Product $product, Attachment $attachment)
    {
        
        if ($product->id != $attachment->resource_id) {
            return response()->json([ 'status' => 2 , 'message' => 'Product and attachment does not related'], Response::HTTP_OK);
        }
        $this->attachmentRepository->destroy($attachment);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
