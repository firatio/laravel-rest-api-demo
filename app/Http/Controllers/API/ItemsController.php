<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
            
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
            
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use \Exception;

class ItemsController extends Controller
{
    /**
     * Get a list of items
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user_id = auth()->user()->id;
            $items = Item::where('user_id', $user_id)->get();

            return response()->json($items, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    /**
     * Store a newly created item
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
				'name' => 'required|string',
				'notes' => 'required|string',
			]);

			$user_id = auth()->user()->id;
			//$user = DB::table('users')->where('id', $user_id)->first();
			//if($user == null) throw new \Exception('INVALID FOREIGN ID');
		
            $item = new Item(request(['name', 'notes']));
            $item->user_id = $user_id;
            $item->save();

            return response()->json($item, 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    /**
     * Get details of the specified item
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        try {
            $item = Item::findOrFail($id);

            Gate::authorize('manipulate-item', $item);

            return response()->json($item, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'No such resource'
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Unauthorized request'
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    /**
     * Update the specified item
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
				'name' => 'required|string',
				'notes' => 'required|string',
			]);

            $item = Item::findOrFail($id);

            Gate::authorize('manipulate-item', $item);

            $item->fill(request(['name', 'notes']));
            $item->save();

            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'No such resource'
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Unauthorized request'
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }

    /**
     * Remove the specified item
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);

            Gate::authorize('manipulate-item', $item);

            $item->delete();

            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'No such resource'
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Unauthorized request'
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid request'
            ], 400);
        }
    }
}
