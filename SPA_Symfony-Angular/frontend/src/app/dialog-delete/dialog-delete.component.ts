import { Component, OnInit, Inject, AfterViewInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA, MatSnackBar } from '@angular/material';
import { FridgeService } from '../fridge/fridge.service';
import { FloorService } from '../fridgeInside/floor.service';
import { FoodService } from '../fridgeInside/food.service';
import { Router } from '@angular/router';
import { Subject } from 'rxjs';

export interface DialogData {
  name: string;
  typeElem: string;
  id: number;
}

export interface DialogDataFood {
  name: string;
  typeElem: string;
  id: number;
  floorIds: Array<number>;
}

@Component({
  selector: 'app-dialog-delete',
  templateUrl: './dialog-delete.component.html',
  styleUrls: ['./dialog-delete.component.css']
})
export class DialogDeleteComponent implements AfterViewInit {
  state = 'successfuly';
  type: string;

  constructor(
    public dialogRef: MatDialogRef<DialogDeleteComponent>,
    @Inject(MAT_DIALOG_DATA) public data: DialogData,
    @Inject(MAT_DIALOG_DATA) public dataFood: DialogDataFood,
    private fridgeService: FridgeService,
    private floorService: FloorService,
    private foodService: FoodService,
    private router: Router,
    private snackBar: MatSnackBar) {}

  ngAfterViewInit(): void {
    if (this.type === 'fridge') {
      this.fridgeService.getErrorListener().subscribe(
        next => {
        },
        error => {
          this.state = 'not';
        }
      );
    } else if (this.type === 'floor') {
      this.floorService.getErrorListener().subscribe(
        next => {
        },
        error => {
          this.state = 'not';
        }
      );
    } else if (this.type === 'floor') {
      this.foodService.getErrorListener().subscribe(
        next => {
        },
        error => {
          this.state = 'not';
        }
      );
    }
  }

  openSnack(type) {
    this.openSnackBar(type + ' ' + this.state + ' deleted!', 'OK');
  }

  onNoClick(): void {
    this.dialogRef.close();
    this.openSnackBar('Deletion Cancelled!', 'OK');
  }

  onYesClick(): void {
    if (this.data.typeElem === 'fridge') {
      this.fridgeService.deleteFridge(this.data.id).subscribe(error => {
        this.floorService.errorListener.error(error);
        this.floorService.errorListener = new Subject<string>();
      }, () => {
        this.fridgeService.getFridges();
        this.router.navigate(['/fridges']);
      });
      this.dialogRef.close(this.data);
      this.type = 'Fridge';
    } else if (this.data.typeElem === 'floor') {
      this.floorService.deleteFloor(this.data.id).subscribe(error => {
        this.floorService.errorListener.error(error);
        this.floorService.errorListener = new Subject<string>();
      });
      this.dialogRef.close(this.data);
      this.type = 'Floor';
    } else if (this.dataFood.typeElem === 'food') {
      this.foodService.deleteFood(this.dataFood.id).subscribe(error => {
        this.floorService.errorListener.error(error);
        this.floorService.errorListener = new Subject<string>();
      });
      this.dialogRef.close(this.dataFood);
      this.type = 'Food';
    }
    this.ngAfterViewInit();
    this.openSnack(this.type);
  }

  openSnackBar(message: string, action: string) {
    this.snackBar.open(message, action, {
      duration: 4000,
    });
  }
}
