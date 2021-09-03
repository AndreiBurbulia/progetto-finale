<?php

namespace App\Http\Controllers;

use App\Category;
use App\Restaurant;
use App\User;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;
        $restaurants = Restaurant::where('user_id', $id)->get();
        if (($restaurants->count() === 0)) {
            $restaurants = false;
            return view('admin.restaurant.index', compact('restaurants'));
        }
        return view('admin.restaurant.index', compact('restaurants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.restaurant.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'categories' => 'required | exists:categories,id',
            'name' => 'required | max:255',
            'description' => 'required | max:1000',
            'img' => 'mimes:jpg,jpeg,png,bmp,gif,svg,webp,JPG,JPEG,PNG,BMP,GIF,SVG,WEBP | max:1050',
            'address' => 'required | max:255',
            'city' => 'required | max:255',
            'cap' => 'required | digits:5',
            'piva' => 'required | digits:11',
        ]);

        if (in_array('img', $validatedData)) {
            // Se si sta caricando l'immagine spostala nello spazio web dedicato all'archiviazione
            $file_path = Storage::put('restaurant_images', $validatedData['img']);
            $validatedData['img'] = $file_path;
        } else {
            // se non esiste, usa l'immagine dentro l'asset
        }

        $id_utente = Auth::user()->id;
        $validatedData['user_id'] = $id_utente;

        $restaurant = Restaurant::create($validatedData);
        $restaurant->categories()->sync($validatedData['categories']);
        return redirect()->route('admin.restaurant.show', $restaurant->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurant.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        $categories = Category::all();
        return view('admin.restaurant.edit', compact('restaurant', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $validatedData = $request->validate([
            'categories' => 'required | exists:categories,id',
            'name' => 'required | max:255',
            'description' => 'required | max:1000',
            'img' => 'mimes:jpg,jpeg,png,bmp,gif,svg,webp,JPG,JPEG,PNG,BMP,GIF,SVG,WEBP | max:1050',
            'address' => 'required | max:255',
            'city' => 'required | max:255',
            'cap' => 'required | digits:5',
            'piva' => 'required | digits:11',
        ]);

        /* 
        Se "img" ovvero l'array di modifica è vuoto, ovvero falso, non fare nulla
        se è vero, quindi nuova immagine, esegui il codice
         */
        if (array_key_exists('img', $validatedData)) {

            Storage::disk('public')->delete($restaurant->img);
            $file_path = Storage::put('restaurant_images', $validatedData['img']);
            $validatedData['img'] = $file_path;
        }

        $restaurant->update($validatedData);
        $restaurant->categories()->attach($validatedData['categories']);
        return redirect()->route('admin.restaurant.show', $restaurant->id);;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        Restaurant::destroy($restaurant->id);
        return redirect()->route('admin.restaurant.index');
    }
}