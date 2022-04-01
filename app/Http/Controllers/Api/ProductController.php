<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateProductFormRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    private $repository;

    public function __construct(Product $product)
    {
        $this->repository = $product;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->repository->paginate(10);

        return response($products);
    }

    /**
     * @param StoreUpdateProductFormRequest $request
     * @return \Illuminate\Http\Response
     *
     */
    public function store(StoreUpdateProductFormRequest $request)
    {
        $data = $request->all();

        $data['url'] = $this->strSlug($data['url']);
        $data['image'] = $this->saveImage($data['image']);

        $product = $this->repository->create($data);

        // Cadastrando as especificações na tabela product_specification
        $specifications = $data['specifications'];
        foreach ($specifications as $specification) {
            $product->specifications()->attach($specification['id']);
        }

        return response(new ProductResource($product));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     *
     */
    public function show($id)
    {
        if (!$product = $this->repository->find($id)) {
            return response(['message' => 'Product not Found!'], 404);
        }

        return response(new ProductResource($product));
    }

    /**
     * @param StoreUpdateProductFormRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     *
     */
    public function update(StoreUpdateProductFormRequest $request, $id)
    {
        if (!$product = $this->repository->find($id)) {
            return response(['message' => 'Product not Found!'], 404);
        }

        $data = $request->all();

        $data['url'] = $this->strSlug($data['url']);

        if (isset($data['image'])) {
            $data['image'] = $this->saveImage($data['image']);

            if (Storage::exists($product->image)) {
                Storage::delete($product->image);
            }
        } else {
            // Coluna image não pode ser nula
            unset($data['image']);
        }

        $product->update($data);
        // Excluindo todas as especificações
        $product->specifications()->detach();

        // Cadastrando as especificações na tabela product_specification
        $specifications = $data['specifications'];
        foreach ($specifications as $specification) {
            $product->specifications()->attach($specification['id']);
        }

        return response(new ProductResource($product));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     *
     */
    public function destroy($id)
    {
        if (!$product = $this->repository->find($id)) {
            return response(['message' => 'Product not Found!'], 404);
        }

        if (Storage::exists($product->image)) {
            Storage::delete($product->image);
        }

        $product->delete();

        return response([], 204);
    }


    /**
     * @param $image
     * @return String | \Exception
     * @throws \Exception
     */
    private function saveImage($image)
    {
        $path = '';

        // Checa se a imagem tem uma strig base 64 válida
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {

            // Pega a extensão
            $type = strtolower($type[1]); // jpg, jpeg, png, gif

            // Checa se é uma imagem
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('Type of image invalid');
            }

            //obtem o arquivo
            $separatorFile = explode(',', $image);
            $file = $separatorFile[1];

            $dir = 'storage/products/';
            $name = Str::random() . '.' . $type;
            $dirName = $dir.$name;

            $path = Image::make($file)->save(public_path($dirName));
        } else {
            throw new \Exception('This is not a valid image');
        }

        $relativePath = 'products/'.$path->basename;

        return $relativePath;
    }

    /**
     * @param string $string
     * @return string
     */
    private function strSlug(string $string): string
    {
        $string = filter_var(mb_strtolower($string), FILTER_SANITIZE_STRIPPED);
        $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

        $slug = str_replace(["-----", "----", "---", "--"], "-",
            str_replace(" ", "-",
                trim(strtr(utf8_decode($string), utf8_decode($formats), $replace))
            )
        );
        return $slug;
    }

}
