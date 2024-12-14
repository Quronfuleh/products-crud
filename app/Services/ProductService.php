<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductService
{
    protected $filePath = 'products.json';


    public function all()
    {
        $products = $this->readData();
        usort($products, function ($a, $b) {
            return strtotime($b['datetime_submitted']) <=> strtotime($a['datetime_submitted']);
        });

        return $products;
    }


    public function find($id)
    {
        $products = $this->readData();
        foreach ($products as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }
        return null;
    }


    public function create(array $data)
    {
        $products = $this->readData();

        $newProduct = [
            'id' => Str::uuid()->toString(),
            'product_name' => $data['product_name'],
            'quantity_in_stock' => (int) $data['quantity_in_stock'],
            'price_per_item' => (float) $data['price_per_item'],
            'datetime_submitted' => Carbon::now()->toDateTimeString(),
        ];

        $products[] = $newProduct;
        $this->writeData($products);
        return $newProduct;
    }

    public function update($id, array $data)
    {
        $products = $this->readData();
        foreach ($products as $index => $product) {
            if ($product['id'] == $id) {
                $products[$index]['product_name'] = $data['product_name'];
                $products[$index]['quantity_in_stock'] = (int) $data['quantity_in_stock'];
                $products[$index]['price_per_item'] = (float) $data['price_per_item'];
                $this->writeData($products);
                return $products[$index];
            }
        }

        return null;
    }


    public function delete($id)
    {
        $products = $this->readData();
        $filtered = array_filter($products, function($p) use($id) {
            return $p['id'] != $id;
        });
        $this->writeData(array_values($filtered));
    }

    protected function readData()
    {
        if (!Storage::exists($this->filePath)) {
            return [];
        }

        $contents = Storage::get($this->filePath);
        $data = json_decode($contents, true);

        return $data ?: [];
    }

    protected function writeData($data)
    {
        Storage::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
