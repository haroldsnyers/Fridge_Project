import { Injectable } from '@angular/core';
import { HttpErrorResponse, HttpClient } from '@angular/common/http';
import { throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AuthLoginData } from '../auth/auth-data.model';
import { Router } from '@angular/router';
import { FridgeCreate, Fridge } from '../fridge/fridge.model';
import { FloorCreate, FloorUpdate } from '../fridgeInside/floor.model';
import { FoodCreate, Food } from '../fridgeInside/food.model';

@Injectable({
    providedIn: 'root'
})
export class ApiService {
    private url = 'http://127.0.0.1:8000';
    // private url = 'http://localhost:3006';

    constructor(private http: HttpClient, private router: Router) {}

    /* User API */

    postLogin(authData: AuthLoginData) {
        return this.http.post<{result: boolean}>(this.url + '/api/user/login', authData).pipe(
            catchError(this.handelError));
    }

    postSignup(authData: AuthLoginData) {
        return this.http.post<{result: boolean}>(this.url + '/api/user/register', authData).pipe(
            catchError(this.handelError));
    }

    /* Fridge API */

    getFridges(userData) {
        return this.http.get(this.url + '/api/fridge/', {params: userData}).pipe(
            catchError(this.handelError));
    }

    addFridges(fridgeData: FridgeCreate) {
        return this.http.post(this.url + '/api/fridge/', fridgeData).pipe(
            catchError(this.handelError));
    }

    updateFridge(fridgeData: Fridge, idFridge: number) {
        return this.http.put(this.url + '/api/fridge/' + idFridge, fridgeData).pipe(
            catchError(this.handelError));
    }

    deleteFridge(idFridge: number) {
        return this.http.delete(this.url + '/api/fridge/' + idFridge).pipe(
            catchError(this.handelError));
    }

    /* Floor API */

    getFloors(fridgeData) {
        return this.http.get(this.url + '/api/floors/', {params: fridgeData})
            .pipe(
                catchError(this.handelError));
    }

    addFloors(floorData: FloorCreate) {
        return this.http
        .post(this.url + '/api/floors/', floorData).pipe(
            catchError(this.handelError));
    }

    updateFloors(floorData: FloorUpdate, idFloor: number) {
        return this.http
        .put(this.url + '/api/floors/' + idFloor, floorData).pipe(
            catchError(this.handelError));
    }

    deleteFloors(idFloor: number) {
        return this.http.delete(this.url + '/api/floors/' + idFloor).pipe(
            catchError(this.handelError));
    }

    /* Food API */

    getFoods(floorData) {
        return this.http.get(this.url + '/api/food/', {params: floorData})
            .pipe(
                catchError(this.handelError));
    }

    addFood(foodData: FoodCreate) {
        return this.http.post(this.url + '/api/food/', foodData)
            .pipe(
                catchError(this.handelError));
    }

    updateFood(foodData: Food, idFood: number) {
        return this.http.put(this.url + '/api/food/' + idFood, foodData)
            .pipe(
                catchError(this.handelError));
    }

    deleteFood(idFood: number) {
        return this.http.delete(this.url + '/api/food/' + idFood)
            .pipe(
                catchError(this.handelError));
    }

    handelError(err: any) {
        if (err instanceof HttpErrorResponse) {
          return throwError(err.error.errors);
        } else {
          return throwError(err.message);
        }
    }
}
