import {
  Component,
  OnInit,
  OnDestroy
} from '@angular/core';
import {
  FormControl
} from '@angular/forms';

@Component({
  selector: 'app-fridginside-list',
  templateUrl: './fridgeInsideList.component.html',
  styleUrls: ['./fridgeInsideList.component.css']
})
export class FridgeInsideListComponent {
  tabLoadTimes: Date[] = [];
  tabs = ['First', 'Second', 'Third'];

  selected = new FormControl(0);
  getTimeLoaded(index: number) {
    if (!this.tabLoadTimes[index]) {
      this.tabLoadTimes[index] = new Date();
    }

    return this.tabLoadTimes[index];
  }



  addTab(selectAfterAdding: boolean) {
    this.tabs.push('New');

    if (selectAfterAdding) {
      this.selected.setValue(this.tabs.length - 1);
    }
  }

  removeTab(index: number) {
    this.tabs.splice(index, 1);
  }
}
