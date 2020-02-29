<?php

namespace App\Http\Controllers\Merchant\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Class constructor.
     *
     * @param \Illuminate\Http\Request $request User Request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->posted = $request->except('_token', '_method');
    }

    public function index()
    {
        $product = Product::where('merchant_id', $this->request->user()->id)->orderBy('id', 'desc')->paginate(10);

        return $this->successResponse(200, '', $product);
    }

    public function create()
    {
        $rules = [
            'name' => 'required',
            'code' => 'required|unique:products,code',
            'image' => 'required|mimes:jpg,jpeg,png,bmp|max:8048',
            'price' => 'required|numeric',
            'categories' => 'required',
            'categories.*.id' => 'required',
        ];

        $messages = [
            'name.required' => 'Nama produk tidak boleh kosong.',
            'code.required' => 'Kode produk tidak boleh kosong.',
            'code.unique' => 'Kode produk sudah digunakan.',
            'image.mimes' => 'Format foto produk harus bertipe jpg,jpeg,png,atau bmp.',
            'image.required' => 'Foto produk tidak boleh kosong.',
            'price.required' => 'Harga produk tidak boleh kosong.',
            'price.numeric' => 'Harga produk tidak boleh mengandung huruf.',
            'categories.*.id' => 'Kategori tidak boleh kosong.',
        ];

        $validate = $this->validator($this->request->all(), $rules, $messages);

        if ($validate) {
            return $this->errorResponse(400, $validate);
        }

        $request = $this->request;

        return \DB::transaction(function () use ($request) {
            $product = Product::create([
                'merchant_id' => $request->user()->id,
                'code' => $request->code,
                'name' => $request->name,
                'image' => $this->storeImage('products/images', $request->image),
                'price' => $request->price,
            ]);

            foreach ($request->categories as $category) {
                $check = Category::where('id', $this->decode($category['id']))->first();
                if (!is_null($check)) {
                    ProductCategory::create([
                        'product_id' => $product->id,
                        'category_id' => $check->id,
                    ]);
                } else {
                    throw new \Exception("Category tidak ditemukan", 1);

                }
            }

            return $this->successResponse(200, 'Produk berhasil ditambahkan', null);
        });
    }

    public function update($code)
    {
        $product = Product::where('id', $this->decode($code))->first();

        if ($product) {
            $rules = [
                'name' => 'max:255',
                'code' => 'unique:products,code',
                'image' => 'mimes:jpg,jpeg,png,bmp|max:8048',
                'price' => 'numeric',
            ];

            $messages = [
                'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter.',
                'code.required' => 'Kode produk tidak boleh kosong.',
                'code.unique' => 'Kode produk sudah digunakan.',
                'image.mimes' => 'Format foto produk harus bertipe jpg,jpeg,png,atau bmp.',
                'image.required' => 'Foto produk tidak boleh kosong.',
                'price.required' => 'Harga produk tidak boleh kosong.',
                'price.numeric' => 'Harga produk tidak boleh mengandung huruf.',
            ];

            $validate = $this->validator($this->request->all(), $rules, $messages);

            if ($validate) {
                return $this->errorResponse(400, $validate);
            }

            foreach ($this->request->all() as $key => $value) {
                if ($key == "image") {
                    $product->{$key} = $this->storeImage('products/images', $value);
                } else {
                    $product->{$key} = $value;
                }
            }

            $product->save();

            return $this->successResponse(200, 'Produk berhasil diupdate', null);
        } else {
            return $this->errorResponse(400, 'Produk tidak ditemukan');
        }
    }

    public function delete($code)
    {
        $product = Product::where('id', $this->decode($code))->first();

        if ($product) {
            $product->delete();

            return $this->successResponse(200, 'Produk berhasil dihapus', null);
        } else {
            return $this->errorResponse(400, 'Produk tidak ditemukan');
        }
    }
}
