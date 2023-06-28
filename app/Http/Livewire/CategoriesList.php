<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class CategoriesList extends Component
{
    use WithPagination;

    public Category $category; 
 
    public bool $showModal = false; 

    public array $active = []; 

    public function openModal() 
    {
        $this->showModal = true;
 
        $this->category = new Category();
    } 

    public function render()
    {
        $categories = Category::paginate(10); 

        $this->active = $categories->mapWithKeys( 
            fn (Category $item) => [$item['id'] => (bool) $item['is_active']]
        )->toArray(); 

        return view('livewire.categories-list', [
            'categories' => $categories, 
        ]);
    }

    public function updatedCategoryName() 
    {
        $this->category->slug = Str::slug($this->category->name);
    } 

    protected function rules(): array 
    {
        return [
            'category.name' => ['required', 'string', 'min:3'],
            'category.slug' => ['nullable', 'string'],
        ];
    }

    public function toggleIsActive($categoryId) 
    {
        Category::where('id', $categoryId)->update([
            'is_active' => $this->active[$categoryId],
        ]);
    } 
    
    
    public function save() 
    {
        $this->validate();
 
        $this->category->save();
 
        $this->reset('showModal');
    }
}
