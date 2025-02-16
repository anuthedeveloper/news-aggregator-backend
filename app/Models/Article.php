<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'source',
        'author',
        'description',
        'content',
        'category',
        'url',
        'url_to_image',
        'published_at',
    ];

    // In Article model (Article.php)
    public function scopeFilterByPreferences(Builder $query, $preferences)
    {
        if ($preferences->sources) {
            $query->whereIn('source', $preferences->sources);
        }
        if ($preferences->categories) {
            $query->whereIn('category', $preferences->categories);
        }
        if ($preferences->authors) {
            $query->whereIn('author', $preferences->authors);
        }
        return $query;
    }

    public function scopeSearchByKeyword(Builder $query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', '%' . $keyword . '%')
            ->orWhere('content', 'like', '%' . $keyword . '%')
            ->orWhere('author', 'like', '%' . $keyword . '%');
        });
    }

    public function scopeFilterByCategory(Builder $query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeFilterBySource(Builder $query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeFilterByDate(Builder $query, $date)
    {
        // Split the date to get year and month
        [$year, $month] = explode('-', $date);

        // Fetch articles for the given year and month
        return $query->whereYear('published_at', $year)
                            ->whereMonth('published_at', $month)
                            ->get();

    }

}
