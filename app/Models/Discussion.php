<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class Discussion extends Model
{
    /** @use HasFactory<\Database\Factories\DiscussionFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'channel_id',
        'title',
        'slug',
        'content',
        'views',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'views' => 'integer',
        ];
    }

    /**
     * Get the user that owns the discussion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the channel that owns the discussion.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get the replies for the discussion.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Get the watchers for the discussion.
     */
    public function watchers(): HasMany
    {
        return $this->hasMany(Watcher::class);
    }

    /**
     * Get the images for the discussion.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Discussion $discussion) {
            $discussion->images()->get()->each->delete();
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'channel' => $this->channel->title,
            'author' => $this->user->name,
        ];
    }
}
