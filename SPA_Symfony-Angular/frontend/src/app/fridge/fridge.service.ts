import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { map } from 'rxjs/operators';
import { Fridge, FridgeCreate } from './fridge.model';
import { Subject, Observable } from 'rxjs';
import { AuthService } from '../auth/auth.service';
import { ApiService } from '../service/api.service';

const urlHost = 'http://127.0.0.1:8000';
// private url = 'http://localhost:3006';

const typeMap = new Map();
const urlImages = urlHost + '/images/fridgeImage/';
typeMap.set('french door fridge', urlImages + 'french_door_fridge.jpg');
typeMap.set('side by side fridge', urlImages + 'side_by_side_fridge.jpg');
typeMap.set('freezerless fridge', urlImages + 'freezerless_fridge.jpg');
typeMap.set('bottom freezer fridge', urlImages + 'bottom_freezer_fridge.jpg');
typeMap.set('top freezer fridge', urlImages + 'top_freezer_fridge.jpg');
typeMap.set('freezer', urlImages + 'freezer.jpg');
typeMap.set('wine fridge', urlImages + 'wine_fridge.jpg');

@Injectable({
    providedIn: 'root'
})
export class FridgeService {
    private fridges: Fridge[] = [];
    private data: JSON;
    private fridgeUpdated = new Subject<{fridges: Fridge[]}>();
    private errorListener: Subject<string> =  new Subject<string>();
    private currentFridge: Fridge;

    private url = 'fridge';

    constructor(
        private http: HttpClient,
        private router: Router,
        private authService: AuthService,
        private api: ApiService) {}

    getFridgeUpdateListener() {
        return this.fridgeUpdated.asObservable();
    }

    setCurrentFridge(idFridge: number) {
        this.currentFridge = this.getFridge(idFridge);
    }

    getCurrentFridge() {
        return this.currentFridge;
    }

    getErrorListener(): Observable<any> {
        return this.errorListener.asObservable();
    }

    getFridges() {
        const email = this.authService.getCurrentUser();
        // tslint:disable-next-line:object-literal-shorthand
        const userData = {email: email};
        this.fridges = []; // reset fridges to zero
        this.api.getItems(userData, this.url)
            .subscribe(getData => {
                this.data = JSON.parse(JSON.stringify(getData));
                for (let i = 0; i < Object.keys(this.data).length; i++) {
                    this.fridges.push(this.data[i]);
                }
                this.fridgeUpdated.next({
                    fridges: [...this.fridges],
                });
            }, error => {
                this.errorListener.error(error);
                this.errorListener = new Subject<string>();
            });
    }

    getFridge(idFridge: number) {
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < this.fridges.length; i++) {
            if (this.fridges[i].id === idFridge) {
                return this.fridges[i];
            }
        }
    }

    addFridge(name: string, type: string, nbrFloors: number) {
        const imagePath = typeMap.get(type);
        const email = this.authService.getCurrentUser();
        const fridgeData: FridgeCreate = {name, type, nbrFloors, imageFridgePath: imagePath, userMail: email};
        // tslint:disable-next-line:object-literal-shorthand
        this.api.addItem(fridgeData, this.url)
          .subscribe(response => {
                this.router.navigate(['/fridges']);
          }, error => {
            this.errorListener.error(error);
            this.errorListener = new Subject<string>();
        });
    }

    updateFridge(idFridge: number, name: string, type: string, nbrOfFloors: number, idUser: number) {
        const imagePath = typeMap.get(type);
        let fridgeData: Fridge | FormData;
        fridgeData = {
            id : idFridge,
            // tslint:disable:object-literal-shorthand
            name: name,
            type: type,
            nbrFloors: nbrOfFloors,
            imageFridgePath: imagePath,
            user_id: idUser
        };
        this.api.updateItem(fridgeData, idFridge, this.url)
          .subscribe(response => {
                this.router.navigate(['/fridges']);
          }, error => {
            this.errorListener.error(error);
            this.errorListener = new Subject<string>();
          });
    }

    deleteFridge(idFridge: number) {
        return this.api.deleteItem(idFridge, this.url);
    }
}
