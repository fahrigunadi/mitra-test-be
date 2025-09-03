<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    #[Scope()]
    public function search(Builder $query, ?string $search = null): void
    {
        $query->when($search, function (Builder $query, $search) {
            $query->whereLike('title', "%{$search}%")
                ->orWhereLike('description', "%{$search}%")
                ->orWhereLike('start_date', "%{$search}%")
                ->orWhereLike('end_date', "%{$search}%");
        });
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
