<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTranslations;

    public $translatable = ['title', 'description'];
    protected $fillable = ['title', 'description', 'price', 'quantity'];

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('products_images') ?: null;
    }
}
