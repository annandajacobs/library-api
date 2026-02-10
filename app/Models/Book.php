<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'description',
        'author_id',
        'genre',
        'published_at',
        'total_copies',
        'available_copies',
        'price',
        'cover_image',
        'status'
    ];

    public function author(): BelongsTo {
        return $this->belongsTo(Author::class);
    }

    public function borrowings(): HasMany {
        return $this->hasMany(Borrowing::class);
    }

    public function isAvailable(): bool {
        return $this->available_copies > 0;
    }

    public function borrow(): void {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
        }
    }

    public function returnBook(): void {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
        }
    }
}
