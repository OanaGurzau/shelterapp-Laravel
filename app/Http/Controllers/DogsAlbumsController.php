<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; //delete it from the library
use App\DogAlbum;
use App\Dog;
use App\MedicalRecord;
use App\Background;


class DogsAlbumsController extends Controller
{
    public $timestamps = false;

    public function index(){
        $albums=DogAlbum::with('Photos', 'Dog')->paginate(9);
        return view('dogs.list_album')->with('albums', $albums);
    }

    public function create(){
        $dogs = Dog::whereNotIn('id', function($query) {
            $query->select('dog_id')->from('dog_albums');
        })->pluck('name', 'id');
        //$albums = DogAlbum::pluck('id');
        //$dogs = Dog::whereNotIn('id', $albums)->pluck('name', 'id');
       return view('dogs.create_album')->with('dogsView', $dogs);
    }

    // public function create(){
    //     DB::table("dogs")->select('*')->whereNotIn('id',function($query) {
            
    //            $query->select('dog_id')->from('dog_albums');
            
    //         })->get();




    //     $dogs = Dog::pluck('name', 'id');
    //    return view('dogs.create_album')->with('dogsView', $dogs);
    // }

    public function store(Request $request)
    {
        $this->validate($request, [
            // 'name' => 'required',
            'cover_image' => 'sometimes|image|max:1999',
            // 'breed' => 'required',
            // 'color' => 'required|regex:/^[a-zA-Z ]+$/', //no number
            // 'microchip' => 'digits:15', //digits also verify if numeric
            // 'description'=> 'required',
            // 'notes'=>'nullable',
        ]);

            // aici // //Get the filename with the extension
            // $filenameWithExt=$request->file('cover_image');
            // if($filenameWithExt!==null){
            //     $filenameWithExt->getClientOriginalName();
         
            // }






            
            


        // $filenameWithExt = $request-> file('cover_image')->getClientOriginalName();


        //aici
        //     //Get only the filename
        // $filename=pathinfo($filenameWithExt, PATHINFO_FILENAME);
        // //    //Get only the extension of the file
        //    $extension=$request->file('cover_image');
        //    if($extension!==null){
        //        $extension->getClientOriginalExtension();
        //    }

        // fara $extension = $request->file('cover_image')->getClientOriginalExtension();

          //Create custom filename

        // aici $filenameToStore=$filename.'_'.time().'.'.$extension;

          //Upload image
// aici
        //   $path=$request-> file('cover_image');
        //   if($path !==null){
        //       $path->storeAs('public/album_covers', $filenameToStore);
        //   }

        // fara $path= $request-> file('cover_image')->storeAs('public/album_covers', $filenameToStore);


       


        // Create Album
        $album=new DogAlbum;
        $album->name = $request->input('name');
        $album->dog_id = $request->input('dog_id');


       


        // //Add new cover image

        //get filename with extension
        
        $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
        
        //get just the filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        //get extension
        $extension=$request->file('cover_image')->getClientOriginalExtension();

        //create new file name
        
        $filenameToStore=$filename.'_'.time().'.'.$extension;

        //Upload Image

        $path=$request->file('cover_image')->storeAs('public/album_covers', $filenameToStore); 

        $album->cover_image = $filenameToStore;
        $album->save();



        return redirect('/albums')->with('success', 'Album Created');

    }



    

 

    public function show($id){

        $album=DogAlbum::with('Photos')->find($id);
        $dogs=$album->dog;
        $background=Background::With('Dog')->find($dogs->id);
        $medicalrecord=MedicalRecord::With('Dog')->find($dogs->id);

        // $album=DogAlbum::with('Photos')->find($id);
        // $dogs=Dog::with('DogAlbum')->find($id);
        // $background=Background::With('Dog')->find($id);
        // $medicalrecord=MedicalRecord::With('Dog')->find($id);
        

        return view('dogs.show_album')
                        ->with('album', $album)
                        ->with('dogs', $dogs)
                        ->with('background', $background)
                        ->with('medicalrecord', $medicalrecord);
    }


    public function edit($id){
        $album=DogAlbum::with('dog')->find($id);
        // $dogs=Dog::with('DogAlbum')->find($id);
        return view('dogs.edit_album')->with('album', $album)
                                    // ->with('dogs', $dogs)
                                    // ->with('background', $background)
                                    // ->with('medicalrecord', $medicalrecord)
                                    ;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            // 'name' => 'required',
            'cover_image' => 'sometimes|image|max:1999',
            // 'breed' => 'required',
            // 'color' => 'required|regex:/^[a-zA-Z ]+$/', //no number
            // 'microchip' => 'digits:15', //digits also verify if numeric
            // 'description'=> 'required',
            // 'notes'=>'nullable',
        ]); 

 



        // //Create Dog

        // $dogs=Dog::find($id);
        // $dogs->name = $request->input('name');
        // $dogs->breed = $request->input('breed');
        // $dogs->color = $request->input('color');
        // $dogs->sex = $request->input('sex');
        // $dogs->microchip = $request->input('microchip');
        // $dogs->birthdate = $request->input('birthdate');
        // $dogs->notes = $request->input('notes');
        // $dogs->description = $request->input('description');
        // $dogs->save();


        // Create Album
        $album=DogAlbum::find($id);
        // $album->name = $request->input('name');
        // $album->cover_image = $request->input('cover_image');
        // $album->dog_id = $request-;

        //Update new photo upon editing

        if ($request->hasFile('cover_image')){
           //get filename with extension
        
                $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
                
                //get just the filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                //get extension
                $extension=$request->file('cover_image')->getClientOriginalExtension();

                //create new file name
                
                $filenameToStore=$filename.'_'.time().'.'.$extension;

                //Upload Image

                $path=$request->file('cover_image')->storeAs('public/album_covers', $filenameToStore);              
           
           
                       
            $oldFilenameToStore=$album->cover_image;
                        

           
            //Update database with new photo
          $album->cover_image=$filenameToStore;

            //Delete old photo
            Storage::delete('public/album_covers/'.$oldFilenameToStore);            
        }
        

        $album->save();
 


        return redirect('/albums')->with('success', 'Album Updated');
    
    
       
    }

    public function destroy($id){
        // $dogs=Dog::find($id);
        $album=DogAlbum::with('dog')->find($id);
        // $background=Background::find($id);
        // $medicalrecord=MedicalRecord::find($id);

        // DogAlbum::destroy($id);
        
        
        $album->delete();

        return redirect('/albums')->with('success', 'Album sters');
        
    }




}
