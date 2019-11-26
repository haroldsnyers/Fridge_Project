import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { Floor, FloorCreate, FloorUpdate } from './floor.model';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../auth/auth.service';
import { FridgeService } from '../fridge/fridge.service';
import { Fridge } from '../fridge/fridge.model';

@Injectable({
    providedIn: 'root'
})
export class FloorService {
    private floors: Floor[] = [];
    private data: JSON;
    private floorUpdated = new Subject<{floors: Floor[]}>();
    private Fridge: Fridge;

    // private url = 'http://127.0.0.1:8000';
    private url = 'http://localhost:3006';

    constructor(
        private http: HttpClient,
        private router: Router,
        private authService: AuthService,
        private fridgeService: FridgeService) {}

    getFloorUpdateListener() {
        return this.floorUpdated.asObservable();
    }

    getListFloorIds() {
        const listFloorId = [];
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < this.floors.length; i++) {
            listFloorId.push(this.floors[i].id);
        }
        return listFloorId;
    }

    getFloors() {
        this.Fridge = this.fridgeService.getCurrentFridge();
        const idFridge = this.Fridge.id.toString();

        const email = this.authService.getCurrentUser();
        // tslint:disable-next-line:object-literal-shorthand
        const fridgeData = {email: email, idFridge: idFridge};
        // tslint:disable-next-line:object-literal-shorthand
        this.floors = []; // reset fridges to zero
        this.http
            .get(
                this.url + '/api/floors/', {params: fridgeData})
            .subscribe(getData => {
                this.data = JSON.parse(JSON.stringify(getData));
                for (let i = 0; i < Object.keys(this.data).length; i++) {
                    this.floors.push(this.data[i]);
                }
                this.floorUpdated.next({
                    floors: [...this.floors],
                });
            });
    }

    getFloor(idFloor: number) {
        // tslint:disable-next-line:prefer-for-of
        for (let i = 0; i < this.floors.length; i++) {
            if (this.floors[i].id === idFloor) {
                return this.floors[i];
            }
        }
    }

    addFloor(name: string, type) {
        const fridge = this.fridgeService.getCurrentFridge();
        const floorData: FloorCreate = {name, type, id_fridge: fridge.id};
        // tslint:disable-next-line:object-literal-shorthand
        this.http
          .post(this.url + '/api/floors/', floorData)
          .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
          });
    }

    updateFloor(idFloor: number, name: string, type: string, idFridge: number) {
        let floorData: FloorUpdate | FormData;
        floorData = {
            id : idFloor,
            // tslint:disable:object-literal-shorthand
            name: name,
            type: type,
            id_fridge: idFridge
        };
        this.http
          .put(this.url + '/api/floors/' + idFloor, floorData)
          .subscribe(response => {
                this.router.navigate(['/fridge/floors']);
          });
    }

    deleteFloor(idFloor: number) {
        return this.http.delete(this.url + '/api/floors/' + idFloor);
    }

}
