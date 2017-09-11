<?php

namespace LaravelVue\Talk\Conversations;

use SebastianBerc\Repositories\Repository;

class ConversationRepository extends Repository
{
    /*
     * this method is default method for repository package
     *
     * @return  \LaravelVue\Talk\Conersations\Conversation
     * */
    public function takeModel()
    {
        return Conversation::class;
    }

    /*
     * check this given user is exists
     *
     * @param   int $id
     * @return  bool
     * */
    public function existsById($id)
    {
        $conversation = $this->find($id);
        if ($conversation) {
            return true;
        }

        return false;
    }

    /*
     * check this given two users is already make a conversation
     *
     * @param   int $user1
     * @param   int $user2
     * @param   int $car_id
     * @return  int|bool
     * */
    public function isExistsAmongTwoUsers($user1, $user2, $car_id)
    {
        $conversation = Conversation::where('user_one', $user1)
            ->where('user_two', $user2)->where('car_id', $car_id);

        if ($conversation->exists()) {
            return $conversation->first()->id;
        }

        return false;
    }

    /*
     * check this given user is involved with this given $conversation
     *
     * @param   int $conversationId
     * @param   int $userId
     * @param   int $car_id
     * @return  bool
     * */
    public function isUserExists($conversationId, $userId, $car_id=null)
    {
        $exists = Conversation::where('id', $conversationId)
            ->where(function ($query) use ($userId, $car_id) {
                $query->where('user_one', $userId)->orWhere('user_two', $userId)->where('car_id', $car_id);
            })
            ->exists();

        return $exists;
    }

    /*
     * retrieve all message thread without soft deleted message with latest one message and
     * sender and receiver user model
     *
     * @param   int $user
     * @param   int $offset
     * @param   int $take
     * @return  collection
     * */
    public function getThreads($user, $order, $offset, $take)
    {
        $conv = new Conversation();
        $conv->authUser = $user;
        $msgThread = $conv->with(['messages' => function ($q) use ($user) {
            return $q->where(function ($q) use ($user) {
                $q->where('user_id', $user)
                    ->where('deleted_from_sender', 0);
            })
                ->orWhere(function ($q) use ($user) {
                    $q->where('user_id', '!=', $user);
                    $q->where('deleted_from_receiver', 0);
                })
                ->latest();
        }, 'messages.sender.image','userone', 'userone.image', 'usertwo', 'usertwo.image', 'car', 'car.image'])
            ->where('user_one', $user)
            ->orWhere('user_two', $user)
            ->offset($offset)
            ->take($take)
            ->orderBy('updated_at', $order)
            ->get();
        
        $threads = [];
        if (count($msgThread) > 0){
            foreach ($msgThread as $thread) {
                $collection = (object) null;
                $conversationWith = ($thread->userone->id == $user) ? $thread->usertwo : $thread->userone;
                $collection->thread = $thread->messages->first();
                $collection->withUser = $conversationWith;
                /*$collection->conversations = $thread;*/
                $collection->car = ($thread->car) ? $thread->car : '';
                $threads[] = $collection;
            }
        }
        
        
        return collect($threads);
    }
    
    /*
     * retrieve all message thread without soft deleted message with latest one message and
     * sender and receiver user model
     *
     * @param   int $user
     * @param   int $offset
     * @param   int $take
     * @return  collection
     * */
    public function threads($user, $order, $offset, $take)
    {
        $conv = new Conversation();
        $conv->authUser = $user;
        $msgThread = $conv->with(['messages' => function ($q) use ($user) {
            return $q->where(function ($q) use ($user) {
                $q->where('user_id', $user)
                    ->where('deleted_from_sender', 0);
            })
                ->orWhere(function ($q) use ($user) {
                    $q->where('user_id', '!=', $user);
                    $q->where('deleted_from_receiver', 0);
                })
                ->latest();
        }, 'messages.sender.image','userone', 'userone.image', 'usertwo', 'usertwo.image', 'car', 'car.image'])
            ->where('user_one', $user)
            ->orWhere('user_two', $user)
            ->offset($offset)
            ->take($take)
            ->orderBy('updated_at', $order)
            ->get();
        
        $threads = [];
        if (count($msgThread) > 0){
            foreach ($msgThread as $thread) {
                $collection = (object) null;
                $conversationWith = ($thread->userone->id == $user) ? $thread->usertwo : $thread->userone;
                $collection->thread = $thread->messages->first();
                $collection->withUser = $conversationWith;
                /*$collection->conversations = $thread;*/
                $collection->car = ($thread->car) ? $thread->car : '';
                $threads[] = $collection;
            }
        }
        
        
        return collect($threads);
    }
    
    /*
 * retrieve all message thread with latest one message and sender and receiver user model
 *
 * @param   int $user
 * @param   int $offset
 * @param   int $take
 * @return  collection
 * */
    public function threadsAll($user, $offset, $take)
    {
        $msgThread = Conversation::with(['messages' => function ($q) use ($user) {
            return $q->latest();
        }, 'userone', 'userone.image', 'usertwo', 'usertwo.image', 'car'])
            ->where('user_one', $user)->orWhere('user_two', $user)
            ->offset($offset)
            ->take($take)
            ->orderBy('updated_at', 'DESC')
            ->get();
        
        $threads = [];
        
        foreach ($msgThread as $thread) {
            $conversationWith = ($thread->userone->id == $user) ? $thread->usertwo : $thread->userone;
            $message = $thread->messages->first();
            $message->user = $conversationWith;
            $threads[] = $message;
        }
        
        return collect($threads);
    }
    
    /*
     * get all conversations by given conversation id
     *
     * @param   int $conversationId
     * @param   int $userId
     * @param   int $offset
     * @param   int $take
     * @return  collection
     * */
    public function getMessagesById($conversationId, $userId, $offset, $take)
    {
        
        return Conversation::whereHas('messages', function ($query) use ($userId, $offset, $take) {
            $query->where(function ($qr) use ($userId) {
                $qr->where('user_id', '=', $userId)
                    ->where('deleted_from_sender', 0);
            })
                ->orWhere(function ($q) use ($userId) {
                    $q->where('user_id', '!=', $userId)
                        ->where('deleted_from_receiver', 0);
                })->skip($offset)->take($take);
        })->with(['messages.sender.image','userone', 'userone.image', 'usertwo', 'usertwo.image'])->find($conversationId);
    }

    /*
     * get all conversations with soft deleted message by given conversation id
     *
     * @param   int $conversationId
     * @param   int $offset
     * @param   int $take
     * @return  collection
     * */
    public function getMessagesAllById($conversationId, $offset, $take)
    {
        return $this->with(['messages' => function ($q) use ($offset, $take) {
            return $q->offset($offset)
                ->take($take)->orderBy('updated_at', 'DESC');
        }, 'userone', 'usertwo', 'userone.image', 'usertwo.image', 'car'])->find($conversationId);
    }
    
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Conversation::class;
    }
}
