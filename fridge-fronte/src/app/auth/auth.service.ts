import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthSignupData, AuthLoginData } from './auth-data.model';
import { Router } from '@angular/router';
import { Subject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root'})
export class AuthService {
    private isAuthenticated = false;
    private authStatusListener = new Subject<boolean>(); // just need to know if user is authenticated
    private currentUser = null;

    // private url = 'http://127.0.0.1:8000';
    private url = 'http://localhost:3006';

    constructor(private http: HttpClient, private router: Router) {}

    getIsAuth() {
        return this.isAuthenticated;
    }

    getAuthStatusListener() {
        return this.authStatusListener.asObservable();
    }

    getCurrentUser() {
        return this.currentUser;
    }

    createUser(email: string, username: string, password: string, passwordConfirmation: string) {
        const authData: AuthSignupData = {email, username, password, passwordConfirmation};
        this.http.post(this.url + '/api/user/register', authData)
            .subscribe(response => {
                this.isAuthenticated = true; // needed to update authentication status
                this.authStatusListener.next(true); // telling everyone who is interested that the user is authenticated
                this.currentUser = authData.email;
                this.router.navigate(['/fridges']);
            });
    }

    loginUser(email: string, password: string) {
        const authData: AuthLoginData = {email, password};
        this.http.post<{result: boolean}>(this.url + '/api/user/login', authData)
            .subscribe(response => {
                if (response.result === true) {
                    this.isAuthenticated = true; // needed to update authentication status
                    this.authStatusListener.next(true); // telling everyone who is interested that the user is authenticated
                    this.currentUser = authData.email;
                }
                this.router.navigate(['/fridges']);
            });
    }

    logout() {
        this.isAuthenticated = false;
        this.authStatusListener.next(false);
        this.router.navigate(['/']);
      }
}
