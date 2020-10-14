<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Ramsey\Collection\Collection;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $blocked
 * @property-read int|null $blocked_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $blocking
 * @property-read int|null $blocking_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $followees
 * @property-read int|null $followees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $followers
 * @property-read int|null $followers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $muting
 * @property-read int|null $muting_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tweet[] $retweets
 * @property-read int|null $retweets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tweet[] $timeline
 * @property-read int|null $timeline_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tweet[] $tweets
 * @property-read int|null $tweets_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * タイムライン
     *
     * @return HasMany
     */
    public function timeline(): HasMany
    {
        // 自分のツイート
        return $this->hasMany(Tweet::class)
            // ツイートの投稿者
            ->with(['user'])
            // リレーションの項目数
            ->withCount(['retweets', 'favorites', 'replies'])
            ->orWhere(function (Builder $query) {
                // 自分がフォローしているユーザー
                $query->whereIn('user_id', $this->followees->pluck('id'))
                    // ブロックされているユーザー
                    ->whereNotIn('user_id', $this->blocked->pluck('id'))
                    // ブロックしているユーザー
                    ->whereNotIn('user_id', $this->blocking->pluck('id'))
                    // ミュートしているユーザー
                    ->whereNotIn('user_id', $this->muting->pluck('id'));
            })
            // 自分がリツイートしたツイート
            ->orWhereIn('id', $this->retweets->pluck('id'))
            // ツイートされた最新順
            ->latest()
            ->take(10);
    }

    /**
     * 自分のツイート
     *
     * @return HasMany
     */
    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class)
            ->latest();
    }

    /**
     * リツイート
     *
     * @return BelongsToMany
     */
    public function retweets(): BelongsToMany
    {
        return $this->BelongsToMany(Tweet::class, 'retweet', 'user_id', 'tweet_id');
    }

    /**
     * すでにフォローしているかどうかを判定する
     *
     * @param User $followee
     * @return bool
     */
    public function hasFollowee(User $followee): bool
    {
        return $this->followees->pluck('id')->contains($followee->id);
    }

    /**
     * 自分がフォローしているユーザー
     *
     * @return BelongsToMany
     */
    public function followees(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follow', 'follower_id', 'followee_id');
    }

    /**
     * 自分をフォローしているユーザー
     *
     * @return BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'follow', 'followee_id', 'follower_id');
    }

    /**
     * 自分がミュートしているユーザー
     *
     * @return BelongsToMany
     */
    public function muting(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'mute', 'user_id', 'muted_id');
    }

    /**
     * 自分がブロックしているユーザー
     *
     * @return BelongsToMany
     */
    public function blocking(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'block', 'user_id', 'blocked_id');
    }

    /**
     * 自分をブロックしているユーザー
     *
     * @return BelongsToMany
     */
    public function blocked(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'block', 'blocked_id', 'user_id');
    }

    /**
     * ユーザーにブロックされているかどうか
     * @param User $user
     * @return bool
     */
    public function isBlockedBy(User $user): bool
    {
        /** @var User[]|Collection $blocked_users */
        $blocked_users = $user->blocked;
        return $blocked_users->contains($this, true);
    }
}
