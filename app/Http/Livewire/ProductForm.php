<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Livewire\Component;

class ProductForm extends Component
{
    public Product $product; 
 
    public bool $editing = false;
 
    public array $categories = [];
 
    public array $listsForFields = [];
 
    public function mount(Product $product): void
    {
        $this->product = $product;
 
        $this->initListsForFields();

        if ($this->product->exists) { 
            $this->editing = true;
 
            $this->product->price = number_format($this->product->price / 100, 2);
 
            $this->categories = $this->product->categories()->pluck('id')->toArray();
        } 
    } 

    public function render()
    {
        return view('livewire.product-form');
    }

    public function save(): RedirectResponse|Redirector
    {
        $this->validate();
 
        $this->product->price = $this->product->price * 100;
 
        $this->product->save();
 
        $this->product->categories()->sync($this->categories);
 
        return redirect()->route('products.index');
    }

    protected function rules(): array
    {
        return [
            'product.name' => ['required', 'string'],
            'product.description' => ['required'],
            'product.country_id' => ['required', 'integer', 'exists:countries,id'],
            'product.price' => ['required'],
            'categories' => ['required', 'array']
        ];
    }

    protected function initListsForFields(): void 
    {
        $this->listsForFields['countries'] = Country::pluck('name', 'id')->toArray();
 
        $this->listsForFields['categories'] = Category::active()->pluck('name', 'id')->toArray();
    }

   

    
}
