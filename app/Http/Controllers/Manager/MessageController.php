<?php

namespace App\Http\Controllers\Manager;


use App\Http\Controllers\BaseManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Starter\Enums\Message\MessageChannel;
use Modules\Starter\Enums\Message\MessageType;
use Modules\Starter\Services\MessageService;

class MessageController extends BaseManagerController
{
	public function items(Request $request)
	{
		$type = $request->input('type', false);

		if ($type == 'all') {
			$type = false;
		}

		$pagination = auth()->user()?->messages(MessageChannel::DATABASE)->when($type, fn($query) => $query->where('type', $type))->paginate();

		return $this->json($pagination);
	}

	public function read($id, MessageService $service)
	{
		$item = auth()->user()->messages()->find($id);
		if ($item) {
			$service->markAsRead($item);
		}
		return $this->json();
	}

	public function readAll(MessageService $service)
	{
		$type = request('type', false);

		if ($type == 'all') {
			$type = false;
		}
		auth()->user()->unread_messages()->when($type, fn($query) => $query->where('type', $type))->get()->each(function ($item) use ($service) {
			$service->markAsRead($item);
		});

		return $this->json();
	}

	public function delete($id)
	{
		$item = auth()->user()->messages()->find($id);
		if ($item) {
			$item->delete();
		}
		return $this->json();
	}

	public function deleteAll()
	{
		$type = request()->input('type', false);
		if ($type == 'all') {
			$type = false;
		}

		auth()->user()->messages()->when($type, fn($query) => $query->where('type', $type))->delete();
		return $this->json();
	}

	public function brief()
	{
		/**
		 * @var Collection $messages
		 */
		$messages = auth()->user()?->unread_messages()->get() ?? collect();

		$result = [
			'all' => $messages->count(),
			'notification' => $messages->where('type', MessageType::NOTIFICATION)->count(),
			'todo' => $messages->where('type', MessageType::TODO)->count(),
		];

		return $this->json($result);
	}
}
