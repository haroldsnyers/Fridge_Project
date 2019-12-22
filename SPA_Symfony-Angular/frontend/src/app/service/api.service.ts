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

    /* GENERAL API */

    getItems(itemData, url) {
        return this.http.get(this.url + '/api/' + url + '/', {params: itemData}).pipe(
            catchError(this.handelError));
    }

    addItem(itemData, url) {
        return this.http.post(this.url + '/api/' + url + '/', itemData).pipe(
            catchError(this.handelError));
    }

    updateItem(itemData, idItem: number, url) {
        return this.http.put(this.url + '/api/' + url + '/' + idItem, itemData).pipe(
            catchError(this.handelError));
    }

    deleteItem(idItem: number, url) {
        return this.http.delete(this.url + '/api/' + url + '/' + idItem).pipe(
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
