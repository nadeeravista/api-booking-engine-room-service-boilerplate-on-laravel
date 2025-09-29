<?php

namespace App\Http\Controllers;

use App\Contracts\RoomServiceInterface;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Room Rates & Prices API",
 *     version="1.0.0",
 *     description="Microservice for managing room rates and prices"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Development server"
 * )
 *
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT",
 *         description="Auth0 JWT Token (enabled when AUTH0_ENABLED=true)"
 *     ),
 *     @OA\Schema(
 *         schema="Room",
 *         type="object",
 *         @OA\Property(property="id", type="string", format="uuid"),
 *         @OA\Property(property="name", type="string", example="Deluxe Suite"),
 *         @OA\Property(property="description", type="string", example="Spacious room with ocean view"),
 *         @OA\Property(property="size", type="string", example="Large"),
 *         @OA\Property(property="amenities", type="array", @OA\Items(type="string"), example={"wifi", "ac", "tv"}),
 *         @OA\Property(property="max_occupancy", type="integer", example=4),
 *         @OA\Property(property="base_occupancy", type="integer", example=2),
 *         @OA\Property(property="property_id", type="string", format="uuid"),
 *         @OA\Property(property="is_active", type="boolean", example=true),
 *         @OA\Property(property="photos", type="array", @OA\Items(ref="#/components/schemas/RoomPhoto")),
 *         @OA\Property(property="primary_photo", ref="#/components/schemas/RoomPhoto"),
 *         @OA\Property(property="created_at", type="string", format="date-time"),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     ),
 *     @OA\Schema(
 *         schema="RoomPhoto",
 *         type="object",
 *         @OA\Property(property="id", type="string", format="uuid"),
 *         @OA\Property(property="filename", type="string", example="room1.jpg"),
 *         @OA\Property(property="url", type="string", example="https://s3.amazonaws.com/bucket/room1.jpg"),
 *         @OA\Property(property="thumbnail_url", type="string", example="https://s3.amazonaws.com/bucket/thumbnails/room1.jpg"),
 *         @OA\Property(property="width", type="integer", example=1920),
 *         @OA\Property(property="height", type="integer", example=1080),
 *         @OA\Property(property="sort_order", type="integer", example=0),
 *         @OA\Property(property="is_primary", type="boolean", example=true),
 *         @OA\Property(property="mime_type", type="string", example="image/jpeg"),
 *         @OA\Property(property="file_size", type="integer", example=500000)
 *     )
 * )
 */
class RoomController extends Controller
{
    public function __construct(
        private RoomServiceInterface $roomService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/rooms",
     *     summary="Get all rooms",
     *     description="Retrieve all rooms for a specific property",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="property_id",
     *         in="query",
     *         description="Property ID to filter rooms",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Room")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $propertyId = $request->get('property_id');

        if (! $propertyId) {
            return response()->json(['error' => 'property_id is required'], 400);
        }

        $rooms = $this->roomService->getRoomsByProperty($propertyId);

        return response()->json(RoomResource::collection($rooms));
    }

    /**
     * @OA\Post(
     *     path="/api/rooms",
     *     summary="Create a new room",
     *     description="Create a new room for a property",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "max_occupancy", "base_occupancy", "property_id"},
     *             @OA\Property(property="name", type="string", example="Deluxe Suite"),
     *             @OA\Property(property="description", type="string", example="Spacious room with ocean view"),
     *             @OA\Property(property="size", type="string", example="Large"),
     *             @OA\Property(property="amenities", type="array", @OA\Items(type="string"), example={"wifi", "ac", "tv"}),
     *             @OA\Property(property="max_occupancy", type="integer", example=4),
     *             @OA\Property(property="base_occupancy", type="integer", example=2),
     *             @OA\Property(property="property_id", type="string", format="uuid"),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(
     *                 property="photos",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="filename", type="string", example="room1.jpg"),
     *                     @OA\Property(property="url", type="string", example="https://s3.amazonaws.com/bucket/room1.jpg"),
     *                     @OA\Property(property="thumbnail_url", type="string", example="https://s3.amazonaws.com/bucket/thumbnails/room1.jpg"),
     *                     @OA\Property(property="width", type="integer", example=1920),
     *                     @OA\Property(property="height", type="integer", example=1080),
     *                     @OA\Property(property="mime_type", type="string", example="image/jpeg"),
     *                     @OA\Property(property="file_size", type="integer", example=500000),
     *                     @OA\Property(property="sort_order", type="integer", example=0),
     *                     @OA\Property(property="is_primary", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Room created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Room")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(CreateRoomRequest $request): JsonResponse
    {
        $room = $this->roomService->createRoom($request->validated());

        return response()->json(new RoomResource($room), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/rooms/{id}",
     *     summary="Get room by ID",
     *     description="Retrieve a specific room by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Room ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Room")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $room = $this->roomService->getRoom($id);

        if (! $room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        return response()->json(new RoomResource($room));
    }

    /**
     * @OA\Put(
     *     path="/api/rooms/{id}",
     *     summary="Update room",
     *     description="Update an existing room",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Room ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Deluxe Suite"),
     *             @OA\Property(property="description", type="string", example="Spacious room with ocean view"),
     *             @OA\Property(property="size", type="string", example="Large"),
     *             @OA\Property(property="amenities", type="array", @OA\Items(type="string"), example={"wifi", "ac", "tv"}),
     *             @OA\Property(property="max_occupancy", type="integer", example=4),
     *             @OA\Property(property="base_occupancy", type="integer", example=2),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Room")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateRoomRequest $request, string $id): JsonResponse
    {
        $room = $this->roomService->updateRoom($id, $request->validated());

        if (! $room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        return response()->json(new RoomResource($room));
    }

    /**
     * @OA\Delete(
     *     path="/api/rooms/{id}",
     *     summary="Delete room",
     *     description="Delete a room",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Room ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Room deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->roomService->deleteRoom($id);

        if (! $deleted) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        return response()->json(null, 204);
    }
}
