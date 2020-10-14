<?php

namespace App;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Tweet
 *
 * @property int $id ID
 * @property int $user_id ユーザーID
 * @property string $content 内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $favorites
 * @property-read int|null $favorites_count
 * @property-read mixed $created
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $retweets
 * @property-read int|null $retweets_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tweet whereUserId($value)
 * @mixin \Eloquent
 */
class Tweet extends Model
{
    use Timestamp;

    protected $fillable = [
        'user_id',
        'content',
    ];

    protected $dates = [
        'created_at'
    ];

    /**
     * 投稿日時
     *
     * @return string
     */
    public function getCreatedAttribute()
    {
        /** @var \DateInterval $diff */
        $diff = $this->created_at->diff(now());

        if ($diff->y > 0) {
            return sprintf('%d 年前', $diff->y);
        }

        if ($diff->m > 0) {
            return sprintf('%d ヶ月前', $diff->m);
        }

        if ($diff->d > 0) {
            return sprintf('%d 日前', $diff->d);
        }

        if ($diff->h > 0) {
            return sprintf('%d 時間前', $diff->h);
        }

        if ($diff->i > 0) {
            return sprintf('%d 分前', $diff->i);
        }

        if ($diff->s > 0) {
            return sprintf('%d 秒前', $diff->s);
        }

        return 'たった今';
    }

    /**
     * 投稿者
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * リツイート
     *
     * @return BelongsToMany
     */
    public function retweets(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'retweet', 'tweet_id', 'user_id');
    }

    /**
     * お気に入り
     *
     * @return BelongsToMany
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite', 'tweet_id', 'user_id');
    }

    /**
     * 返信
     *
     * @return BelongsTo
     */
    public function replies(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id', 'reply_id')
            ->withDefault();
    }
}
