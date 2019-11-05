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

    constructor(private http: HttpClient, private router: Router, private authService: AuthService) {}

    getFridgeUpdateListener() {
        return this.fridgeUpdated.asObservable();
    }

    getFridges() {
        const email = this.authService.getCurrentUser();
        // tslint:disable-next-line:object-literal-shorthand
        const Userdata = {email: email};
        this.http
            .get(
                'http://localhost:3006/api/fridge/list', {params: Userdata})
            .subscribe(getData => {
                this.data = JSON.parse(JSON.stringify(getData));
                console.log(this.data);
                for (let i = 0; i < Object.keys(this.data).length; i++) {
                    this.fridges.push(this.data[i]);
                }
                console.log(this.fridges);
                this.fridgeUpdated.next({
                    fridges: [...this.fridges],
                });
                console.log(getData);
            });
    }

    getFridge(idFridge: string) {
        // subscribe in post-create
        return this.http.get(
          'http://localhost:3006/api/fridge/' + idFridge + '/floor/list');
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
          .post<{fridge: Fridge }>('http://localhost:3006/api/fridge/create', fridgeData)
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
          .put('http://localhost:3006/api/fridge/edit' + idFridge, fridgeData)
          .subscribe(response => {
                this.router.navigate(['/']);
          });
    }

    deleteFridge(fridgeId: string) {
        return this.http.delete('http://localhost:3006/api/fridge/delete/' + fridgeId);
    }
}
