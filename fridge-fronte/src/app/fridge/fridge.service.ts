import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { map } from 'rxjs/operators';
import { Fridge } from './fridge.model';
import { Subject } from 'rxjs';
import { AuthService } from '../auth/auth.service';

@Injectable({
    providedIn: 'root'
})
export class FridgeService {
    private fridges: Fridge[] = [];
    private data: JSON;
    private fridgeUpdated = new Subject<{fridges: Fridge[]}>();

    // private url = 'http://127.0.0.1:8000';
    private url = 'http://localhost:3006';

    constructor(private http: HttpClient, private router: Router, private authService: AuthService) {}

    getFridgeUpdateListener() {
        return this.fridgeUpdated.asObservable();
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

    getFridge(idFridge: string) {
        // subscribe in post-create
        return this.http.get(
            this.url + '/api/fridge/' + idFridge + '/floor/list');
    }

    addFridge(name: string, type: string, nbrOfFloors: string) {
        const email = this.authService.getCurrentUser();
        const fridgeData = new FormData();
        fridgeData.append('name', name);
        fridgeData.append('type', type);
        fridgeData.append('nbrFloors', nbrOfFloors);
        fridgeData.append('userMail', email);
        // tslint:disable-next-line:object-literal-shorthand
        this.http
          .post<{fridge: Fridge }>(this.url + '/api/fridge/', fridgeData)
          .subscribe(() => {
            this.router.navigate(['/fridges']);
          });
    }

    updateFridge(idFridge: string, name: string, type: string, nbrOfFloors: string, idUser: string) {
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
                this.router.navigate(['/']);
          });
    }

    deleteFridge(idFridge: string) {
        return this.http.delete(this.url + '/api/fridge/' + idFridge);
    }
}
