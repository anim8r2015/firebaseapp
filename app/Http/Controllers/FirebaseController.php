<?php
namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;


class FirebaseController extends Controller {

    //add new collection
    public function createCollection(Request $request){
        $db = new FirestoreClient([
            'projectId' => $projectId,
        ]);
        $data = json_encode($request->post());
        $data = json_decode($data);
        
        $responseMessage = '';

        if($data!=null){
            $collection = $db->collection($data->collectionName)->document($data->documentName);
            $collectionData = json_encode($data->collectionData);
            $collectionData = json_decode($collectionData,true);

            $collection->set($collectionData,['merge'=>false]);
            $responseMessage = $data->collectionName . ' created succesfully.';
        } else {
            $responseMessage = 'Error creating a new collection.';
        }
        
        return $this->sendResponse('response', $responseMessage);

    }

    //update collection
    public function updateCollection(Request $request){
        $db = new FirestoreClient();
        $data = json_encode($request->post());
        $data = json_decode($data);

        $responseMessage = '';

        if($data!=null){
            $collection = $db->collection($data->collectionName)->document($data->documentName);
            $collectionData = json_encode($data->collectionData);
            $collectionData = json_decode($collectionData,true);

            $collection->set($collectionData,['merge'=>true]);
            $responseMessage = $data->collectionName . ' updated succesfully.';
        } else {
            $responseMessage = 'Error updating collection.';
        }
        return $this->sendResponse('response', $responseMessage);

    }

    //query data in a collection
    public function getDocsByCollection($collection, Request $request){
        $db = new FirestoreClient();
        $collectionRef = $db->collection($collection);

        //$query = $citiesRef->where('name', '<>', null);
        $documents = $collectionRef->documents();
        $collections = [];
        foreach ($documents as $document) {
            if ($document->exists()) {               
                array_push($collections, $document->data());
            }
        }
        return $this->sendResponse($collections, $collection.' data retrieved successfully!');
    }

    public function getDocsByCollectionCriteria($collection, $field, $value, Request $request){
        $db = new FirestoreClient();
        $collectionRef = $db->collection($collection);
        $query = $collectionRef->where($field, '=', $value);
        $documents = $query->documents();
        $collections = [];
        foreach ($documents as $document) {
            if ($document->exists()) {               
                array_push($collections, $document->data());
            }
        }
        return $this->sendResponse($collections, $collection.' data retrieved successfully!');
    }

    //query a compound collection by traversing or otherwise

}
