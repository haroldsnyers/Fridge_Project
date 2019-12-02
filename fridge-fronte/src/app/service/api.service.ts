import { Injectable } from '@angular/core';
import { HttpErrorResponse, HttpClient } from '@angular/common/http';
import { throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AuthLoginData } from '../auth/auth-data.model';
import { Router } from '@angular/router';

@Injectable({
    providedIn: 'root'
})
export class ApiService {
    private url = 'http://127.0.0.1:8000';
    // private url = 'http://localhost:3006';

    constructor(private http: HttpClient, private router: Router) {}

    postLogin(authData: AuthLoginData) {
        return this.http.post<{result: boolean}>(this.url + '/api/user/login', authData).pipe(
            catchError(this.handelError));
    }

    postSignup(authData: AuthLoginData) {
        return this.http.post<{result: boolean}>(this.url + '/api/user/register', authData).pipe(
            catchError(this.handelError));
    }

    handelError(err: any) {
        if (err instanceof HttpErrorResponse) {
          return throwError(err.error.error);
        } else {
          return throwError(err.message);
        }
    }
}
