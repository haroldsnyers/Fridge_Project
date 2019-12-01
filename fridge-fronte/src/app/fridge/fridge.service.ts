import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { map } from 'rxjs/operators';
import { Fridge, FridgeCreate } from './fridge.model';
import { Subject } from 'rxjs';
import { AuthService } from '../auth/auth.service';

@Injectable({
    providedIn: 'root'
})
export class FridgeService {
    private fridges: Fridge[] = [];
    private data: JSON;
    private fridgeUpdated = new Subject<{fridges: Fridge[]}>();
    private currentFridge: Fridge;

    private url = 'http://127.0.0.1:8000';
    // private url = 'http://localhost:3006';

    constructor(
        private http: HttpClient,
        private router: Router,
        private authService: AuthService) {}

    getFridgeUpdateListener() {
        return this.fridgeUpdated.asObservable();
    }

    setCurrentFridge(idFridge: number) {
        this.currentFridge = this.getFridge(idFridge);
    }

    getCurrentFridge() {
        return this.currentFridge;
    }

    getFridges() {
        const email = this.authService.getCurrentUser();
        // tslint:disable-next-line:object-literal-shorthand
        const Userdata = {email: email};
        this.fridges = []; // reset fridges to zero
        this.http
            .get(
                this.url + '/api/fridge/', {params: Userdata})
            .subscribe(getData => {
                this.data = JSON.parse(JSON.stringify(getData));
                for (let i = 0; i < Object.keys(this.data).length; i++) {
                    this.fridges.push(this.data[i]);
                }
                this.fridgeUpdated.next({
                    fridges: [...this.fridges],
                });
            });
    }

    getFridge(idFridge: number) {
        // subscribe in post-create
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < this.fridges.length; i++) {
            if (this.fridges[i].id === idFridge) {
                return this.fridges[i];
            }
        }
        // return this.http.get(
        //     this.url + '/api/fridge/' + idFridge);
    }

    addFridge(name: string, type: string, nbrFloors: number) {
        const email = this.authService.getCurrentUser();
        const fridgeData: FridgeCreate = {name, type, nbrFloors, userMail: email};
        // tslint:disable-next-line:object-literal-shorthand
        this.http
          .post(this.url + '/api/fridge/', fridgeData)
          .subscribe(response => {
                this.router.navigate(['/fridges']);
          });
    }

    updateFridge(idFridge: number, name: string, type: string, nbrOfFloors: number, idUser: number) {
        let fridgeData: Fridge | FormData;
        fridgeData = {
            id : idFridge,
            // tslint:disable:object-literal-shorthand
            name: name,
            type: type,
            nbrFloors: nbrOfFloors,
            user_id: idUser
        };
        this.http
          .put(this.url + '/api/fridge/' + idFridge, fridgeData)
          .subscribe(response => {
                this.router.navigate(['/fridges']);
          });
    }

    deleteFridge(idFridge: number) {
        return this.http.delete(this.url + '/api/fridge/' + idFridge);
    }
}
