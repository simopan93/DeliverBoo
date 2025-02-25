<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Plate;
use App\Restaurant;
use App\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RestaurantsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $user = Auth::user();
        $ristoranti = Restaurant::where('user_id',$user->id)->paginate(5);
        return view('admin.restaurants.index',compact('ristoranti','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $types = Type::all();
        return view('admin.restaurants.create', compact('types','user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        $request->validate($this->validationData(), $this->validationError());
        $user = Auth::user();
        
        $data = $request->all();
        $new_restaurant = new Restaurant();
        
        
        if(array_key_exists('cover', $data)){
            
            // preno il nome originale dell'immagine
            $data['cover_original_name'] = $request->file('cover')->getClientOriginalName();
            
            // salvare l'immagine e salvare il percorso
            $image_path = Storage::put('uploads', $data['cover']);
            $data['cover'] = $image_path;
            $new_restaurant-> cover_up_by_user = 1;
        }
        else{
            $data['cover'] = 'https://via.placeholder.com/350x290/45CCBC/FFFFFF?Text=DeliverBoo+plates';
            $data['cover_original_name'] = "DeliveBoo Restaurant";
        }
        
        
        $new_restaurant->user_id = $user->id;
        $new_restaurant->fill($data);
        $new_restaurant->slug = Restaurant::generateSlug($new_restaurant->name);
        $new_restaurant->save();


        if (array_key_exists('types',$data)){
            $new_restaurant->types()->attach($data['types']);
        }
    
        return redirect()->route('admin.miei-ristoranti.piatti.index',$new_restaurant->slug);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $user = Auth::user();
        $restaurant = Restaurant::where('slug',$slug)->first();
        $types = Type::all();
        return view('admin.restaurants.edit', compact('restaurant','types','user'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate($this->validationData(), $this->validationError());
        $restaurant = Restaurant::where('id', $id)->first();
        $form_data = $request->all();

        if(array_key_exists('cover', $form_data)){
            // elimino la vecchia immagine (se esiste)
            if($restaurant->cover){
                Storage::delete($restaurant->cover);
            }
            //  prendere il nome della vecchia immagine
            $form_data['cover_original_name'] = $request->file('cover')->getClientOriginalName(); 
            //  salvare l'immagine da salvare e prendere il percorso da fillare
            $image_path = Storage::put('uploads', $form_data['cover']);
            $form_data['cover'] = $image_path;

        }

        if($form_data['name'] != $restaurant->name  ){
            $form_data['slug'] = Restaurant::generateSlug($form_data['name']);
        }

        $restaurant->cover_up_by_user = 1;
        $restaurant->update($form_data);

        if(array_key_exists('types',$form_data)){
            $restaurant->types()->sync($form_data['types']);
        }else{
            $restaurant->types()->detach();
        }

        return redirect()->route('admin.miei-ristoranti.piatti.index',$restaurant->slug);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $restaurant = Restaurant::where('id', $id)->first();
      $piattiMenu = Plate::where('restaurant_id', $id)->get();
      if($piattiMenu){
          foreach ($piattiMenu as $imgPiatto) {
              Storage::delete($imgPiatto->cover);
          }
      }
      if($restaurant->cover){
          Storage::delete($restaurant->cover);
      }
      $restaurant->delete();

      return redirect()->route('admin.miei-ristoranti.index')->with('deleted', 'Post eliminato correttamente');
    
    }

    public function validationData(){
        return[
            'name' => 'required',
            'city' => 'required | min:2',
            'address' => 'required | min:5',
            'zip_code' => 'required| size:5',
            'phone_number' => 'required | min:5 | max:20',
            'p_iva' => 'required | size:11 ',
            'types' => 'required',
            'cover' => 'nullable|mimes:jpeg,jpg,bmp,svg,webp,png|max:32000'
        ]; 
    }

    public function validationError(){
        return[
            'name.required' => 'Inserire il nome del ristorante',

            'city.required'=> 'Inserire la città',
            'city.min'=> 'Il nome della città non è valido',

            'address.required'=> "Inserire l'indirizzo",
            'address.min'=> 'Inserire un indirizzo valido',

            'zip_code.required'=> 'Inserire il CAP',
            // 'zip_code.integer'=> 'Inserire un CAP valido (solo numeri)',
            'zip_code.size'=> 'Inserire un CAP valido (5 numeri)',

            'phone_number.required'=> 'Inserire un numero di telefono',
            'phone_number.min'=> 'Inserire un numero di telefono valido',
            'phone_number.max'=> 'Inserire un numero di telefono valido',

            'p_iva.required'=> 'Inserire la Partita IVA',
            'p_iva.size'=> 'Partita IVA non valida(11 numeri)',
            // 'p_iva.integer'=> 'Partita IVA non valida (solo numeri)',
            'types.required'=>'Inserire almeno una categoria',

            'cover.mimes' => 'Il file deve essere una immagine jpeg, jpg, bmp, svg, webp o png',
            'cover.max' => 'Dimensione del file troppo grande'
        ];
    }

//     private function makeImagePath($cover){
//         if($cover){
//             $cover = url('storage/' . $cover);
//         }else{
//             $cover = 'https://via.placeholder.com/350x290/45CCBC/FFFFFF?Text=DeliverBoo+plates';
//         }
    
//     return $cover;
// }
}
