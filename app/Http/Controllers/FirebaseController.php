<?php
namespace App\Http\Controllers;

// Imports the Cloud Storage client library.
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;


class FirebaseController extends Controller {

    //add new collection
    public function createCollection(Request $request){

        $projectId = env('PROJECT_ID');
        $serviceAccountPath = env('PROJECT_KEY_PATH');
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $db = new FirestoreClient($config);
           // [      'projectId' => $projectId,   ]);
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
        $projectId = env('PROJECT_ID');
        $serviceAccountPath = env('PROJECT_KEY_PATH');
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $db = new FirestoreClient($config);
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
        $projectId = env('PROJECT_ID');
        $serviceAccountPath = env('PROJECT_KEY_PATH');
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $db = new FirestoreClient($config);
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
        $projectId = env('PROJECT_ID');
        $serviceAccountPath = env('PROJECT_KEY_PATH');
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $db = new FirestoreClient($config);
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

    /*
    function auth_cloud_explicit($projectId, $serviceAccountPath) {
    //# Explicitly use service account credentials by specifying the private key
    //# file.
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $storage = new StorageClient($config);

        //# Make an authenticated API request (listing storage buckets)
        foreach ($storage->buckets() as $bucket) {
            printf('Bucket: %s' . PHP_EOL, $bucket->name());
        } 
    }*/

    //query a compound collection by traversing or otherwise

}
