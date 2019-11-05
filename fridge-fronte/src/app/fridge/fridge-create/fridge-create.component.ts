import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-fridge-list',
  templateUrl: './fridge-create.component.html',
  styleUrls: ['./fridge-create.component.css']
})
export class FridgeCreateComponent {
    enteredValue = '';
    newFridge = 'NO CONTENT';

    onAddFridge() {
        this.newFridge = this.enteredValue;
    }
}
