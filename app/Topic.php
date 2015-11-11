<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
	protected $fillable = ['user_id', 'team_id', 'name', 'postCount'];

	public function posts()
	{
		return $this->hasMany('\App\TopicPost');
	}

	public function postCount()
	{
		return $this->hasMany('\App\TopicPost')
			->count();
	}

	public function user()
	{
		return $this->belongsTo('\App\User');
	}

	public function isStarred($user_id)
	{
		$count = DB::table('topic_stars')
			->whereUserId($user_id)
			->whereTopicId($this->id)
			->count();

		return $count > 0;
	}

	public function starCount()
	{
		return TopicStar::whereTopicId($this->id)
			->count();
	}

	public function createStar($user_id)
	{
		return TopicStar::create([
			'user_id' => $user_id,
			'topic_id' => $this->id
		]);
	}

	public function deleteStar($user_id)
	{
		return TopicStar::whereUserId($user_id)
			->whereTopicId($this->id)
			->delete();
	}

	public function viewCount()
	{
		return TopicView::whereTeamId(Auth::user()->id)
			->whereTopicId($this->id)
			->count();
	}

	public function likeCount()
	{
		return Topic::where('topics.id', '=', $this->id)
			->where('topics.deleted', '=', 0)
			->join('topic_posts', 'topic_posts.topic_id', '=', 'topics.id')
			->where('topic_posts.deleted', '=', 0)
			->join('topic_post_user_likes', 'topic_post_user_likes.topic_post_id', '=', 'topic_posts.id')
			->count();
	}

	public function users()
	{
		return Topic::where('topics.id', '=', $this->id)
			->where('topics.deleted', '=', 0)
			->join('topic_posts', 'topic_posts.topic_id', '=', 'topics.id')
			->where('topic_posts.deleted', '=', 0)
			->join('users', 'users.id', '=', 'topic_posts.user_id')
			->where('users.deleted', '=', 0)
			->distinct()
			->get(['users.name', 'users.image']);
	}

	public function updatePostCount()
	{
		Topic::whereId($this->id)
			->update([
				'post_count' => $this->postCount()
			]);
	}

	public function updateLikeCount()
	{
		DB::table('topics')
			->whereId($this->id)
			->update([
				'like_count' => $this->likeCount()
			]);
	}

	public function updateViewCount()
	{
		$this->view_count = $this->viewCount();
		return $this->save();
	}

	public function isUnread()
	{
		$query = DB::query("
			SELECT COUNT(*) as topic_count
			FROM topics t
			WHERE NOT exists(
				SELECT tuv.topic_id, max(tuv.created_at) as last_view, max(tp.created_at) as last_post
				from topic_user_views tuv
				inner join topic_posts as tp
				on tuv.topic_id=tp.topic_id and tuv.created_at > tp.created_at
				group by topic_id
				having t.id=topic_id)
				AND t.id = {$this->id}
		");

		return $query[0]->topic_count > 0;
	}

	public function notifications()
	{
		return $this->hasMany('TopicNotification');
	}

	public function send_notifications($post)
	{
		$body = "
		<table>
			<tr>
				<td>
					<img src='" . url('/image?image=' . $post->user->image . '&size=50') . "'>
				</td>
				<td>
					<strong>{$post->user->name}</strong>
					{$post->body}
					<p>
						<a href='" . url('profile#/profile/social/topics/' . $this->id) . "'>
							Continue Reading
						</a>
					</p>
				</td>
			</tr>
		</table>
		";

		foreach ($this->notifications as $notification) {
			if ($notification->user_id == Auth::user()->id || !$notification->user) {
				continue;
			}

			$email = Email::create([
				'to' => $notification->user->email,
				'subject' => "New Post In {$this->name}",
				'body' => render("templates.email", [
					'header' => "New Post In {$this->name}",
					'content' => $body
				])
			]);

			Queue::create([
				'email_id' => $email->id,
				'type' => 'send_email',
			]);
		}
	}

	public function removeNotifications($user_id)
	{
		return TopicUserNotification::whereUserId($user_id)
			->whereTopicId($this->id)
			->delete();
	}

	public function addNotifications($user_id)
	{
		$this->removeNotifications($user_id);

		return DB::table('topic_user_notifications')
			->insert([
				'topic_id' => $this->id,
				'user_id' => $user_id,
				'type' => 'watching'
			]);
	}
}