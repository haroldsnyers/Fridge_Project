import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { AuthSignupData, AuthLoginData } from './auth-data.model';
import { Router } from '@angular/router';
import { Subject, Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { ApiService } from '../service/api.service';

@Injectable({ providedIn: 'root'})
export class AuthService {
    private isAuthenticated = false;
    private authStatusListener = new Subject<boolean>(); // just need to know if user is authenticated
    private errorListener: Subject<string> =  new Subject<string>();
    private currentUser = null;

    private emailHistory = '';
    private passwordHistory = '';
    private usernameHistory = '';

    constructor(
        private http: HttpClient,
        private router: Router,
        private api: ApiService) {}

    getIsAuth() {
        return this.isAuthenticated;
    }

    getAuthStatusListener() {
        return this.authStatusListener.asObservable();
    }

    getErrorListener(): Observable<any> {
        return this.errorListener.asObservable();
    }

    getCurrentUser() {
        return this.currentUser;
    }

    setHistorySignup(email, username, password) {
        this.emailHistory = email;
        this.passwordHistory = password;
        this.usernameHistory = username;
    }

    getHistorySignup() {
        const listHistory = [this.emailHistory, this.usernameHistory, this.passwordHistory];
        return listHistory;
    }

    setHistory(email, password) {
        this.emailHistory = email;
        this.passwordHistory = password;
    }

    getHistory() {
        const listHistory = [this.emailHistory, this.passwordHistory];
        return listHistory;
    }

    createUser(email: string, username: string, password: string, passwordConfirmation: string) {
        const authData: AuthSignupData = {email, username, password, passwordConfirmation};
        console.log(authData);
        this.api.postSignup(authData)
            .subscribe(response => {
                this.isAuthenticated = true; // needed to update authentication status
                this.authStatusListener.next(true); // telling everyone who is interested that the user is authenticated
                this.currentUser = authData.email;
                this.router.navigate(['/fridges']);
            }, error => {
                this.errorListener.error(error);
                this.authStatusListener.next(false);
                this.errorListener = new Subject<string>();
            });
    }

    loginUser(email: string, password: string) {
        const authData: AuthLoginData = {email, password};
        this.api.postLogin(authData)
            .subscribe(
                response => {
                    if (response.result === true) {
                        this.isAuthenticated = true; // needed to update authentication status
                        this.authStatusListener.next(true); // telling everyone who is interested that the user is authenticated
                        this.currentUser = authData.email;
                    }
                    this.router.navigate(['/fridges']);
                }, error => {
                    this.errorListener.error(error);
                    this.authStatusListener.next(false);
                    this.errorListener = new Subject<string>();
                });
    }

    logout() {
        this.isAuthenticated = false;
        this.authStatusListener.next(false);
        this.router.navigate(['/']);
    }
}
