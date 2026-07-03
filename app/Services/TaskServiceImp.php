<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\CareGiver;
use App\Models\CareSeeker;
use App\Models\Booking;
use App\Models\Task;
use App\Dto\TaskDto;
use App\Enums\TaskType;
use App\Models\User;

class TaskServiceImp implements TaskService
{
    public function create(User $user, array $data): TaskDto
    {
        $caregiver = CareGiver::where('user_uid', $user->uid)->first();
        $booking = Booking::findOrFail($data['bookingId']);

        if (! $booking->care_giver_uid || ! $caregiver || $booking->care_giver_uid !== $caregiver->uid) {
            throw new \InvalidArgumentException("You don't have permission to create tasks on this booking.");
        }

        $task = Task::create([
            'task_name' => $data['taskName'],
            'type' => $data['type'],
            'booking_id' => $data['bookingId'],
        ]);

        return TaskDto::fromArray($task->toArray());
    }

    public function getById(int $taskId): ?TaskDto
    {
        $task = Task::find($taskId);

        if (! $task) {
            return null;
        }

        return TaskDto::fromArray($task->toArray());
    }

    public function getAll(User $user): array
    {
        if ($user->role !== Role::ADMIN->value) {
            throw new \InvalidArgumentException("Only administrators can view all tasks.");
        }

        return Task::all()->map(fn ($task) => TaskDto::fromArray($task->toArray()))->all();
    }

    public function getAllByBooking(int $bookingId, User $user): array
    {
        $booking = Booking::findOrFail($bookingId);

        $seeker = CareSeeker::where('user_uid', $user->uid)->first();
        $caregiver = CareGiver::where('user_uid', $user->uid)->first();

        if (! $seeker && ! $caregiver) {
            throw new \InvalidArgumentException("You don't have permission to view tasks for this booking.");
        }

        $hasAccess = false;
        if ($seeker && $booking->care_seeker_uid === $seeker->uid) {
            $hasAccess = true;
        }
        if ($caregiver && $booking->care_giver_uid === $caregiver->uid) {
            $hasAccess = true;
        }

        if (! $hasAccess) {
            throw new \InvalidArgumentException("You don't have permission to view tasks for this booking.");
        }

        return $booking->tasks->map(fn ($task) => TaskDto::fromArray($task->toArray()))->all();
    }

    public function update(User $user, int $taskId, array $data): TaskDto
    {
        $caregiver = CareGiver::where('user_uid', $user->uid)->first();
        $task = Task::findOrFail($taskId);

        if ($task->booking->care_giver_uid !== ($caregiver->uid ?? null)) {
            throw new \InvalidArgumentException("You don't have permission to update this task.");
        }

        if (isset($data['taskName'])) {
            $task->task_name = $data['taskName'];
        }
        if (isset($data['type'])) {
            $task->type = $data['type'];
        }
        if (isset($data['bookingId'])) {
            $booking = Booking::findOrFail($data['bookingId']);
            $task->booking_id = $booking->id;
        }

        $task->save();
        return TaskDto::fromArray($task->toArray());
    }

    public function delete(User $user, int $taskId): string
    {
        $caregiver = CareGiver::where('user_uid', $user->uid)->first();
        $task = Task::findOrFail($taskId);

        if ($task->booking->care_giver_uid !== ($caregiver->uid ?? null)) {
            throw new \InvalidArgumentException("You don't have permission to delete this task.");
        }

        $task->delete();
        return "Task deleted successfully.";
    }
}