import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { Floor } from './floor.model';
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

    getFloors() {
        this.Fridge = this.fridgeService.getCurrentFridge();
        const idFridge = this.Fridge.id.toString();

        const email = this.authService.getCurrentUser();
        // tslint:disable-next-line:object-literal-shorthand
        const FridgeData = {email: email, idFridge: idFridge};
        // tslint:disable-next-line:object-literal-shorthand
        this.floors = []; // reset fridges to zero
        this.http
            .get(
                this.url + '/api/fridge/', {params: FridgeData})
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
}
