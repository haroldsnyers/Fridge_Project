import { Component, OnInit, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { FridgeService } from '../fridge/fridge.service';
import { FloorService } from '../fridgeInside/floor.service';

export interface DialogData {
  name: string;
  typeElem: string;
  id: number;
}

@Component({
  selector: 'app-dialog-delete',
  templateUrl: './dialog-delete.component.html',
  styleUrls: ['./dialog-delete.component.css']
})
export class DialogDeleteComponent {

  constructor(
    public dialogRef: MatDialogRef<DialogDeleteComponent>,
    @Inject(MAT_DIALOG_DATA) public data: DialogData,
    private frigeService: FridgeService,
    private floorService: FloorService) {}

  onNoClick(): void {
    this.dialogRef.close();
  }

  onYesClick(): void {
    if (this.data.typeElem === 'fridge') {
      this.frigeService.deleteFridge(this.data.id);
    } else if (this.data.typeElem === 'floor') {
      this.floorService.deleteFloor(this.data.id).subscribe(() => {
        this.dialogRef.close();
        this.floorService.getFloors();
      });
    }
  }
}
