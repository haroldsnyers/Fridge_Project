import {
  Component,
  OnInit,
  OnDestroy,
  ViewEncapsulation,
  ViewChild,
  AfterViewInit,
  ContentChild
} from '@angular/core';
import {
  FormControl
} from '@angular/forms';
import { MatSort } from '@angular/material/sort';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';

import { Floor } from '../floor.model';
import { Food } from '../food.model';
import { Subscription } from 'rxjs';

import { FloorService } from '../floor.service';
import { AuthService } from 'src/app/auth/auth.service';
import { Fridge } from 'src/app/fridge/fridge.model';
import { FridgeService } from 'src/app/fridge/fridge.service';

export interface UserData {
  id: string;
  name: string;
  progress: string;
  color: string;
}

/** Constants used to fill up our data base. */
const COLORS: string[] = [
  'maroon', 'red', 'orange', 'yellow', 'olive', 'green', 'purple', 'fuchsia', 'lime', 'teal',
  'aqua', 'blue', 'navy', 'black', 'gray'
];
const NAMES: string[] = [
  'Maia', 'Asher', 'Olivia', 'Atticus', 'Amelia', 'Jack', 'Charlotte', 'Theodore', 'Isla', 'Oliver',
  'Isabella', 'Jasper', 'Cora', 'Levi', 'Violet', 'Arthur', 'Mia', 'Thomas', 'Elizabeth'
];

@Component({
  selector: 'app-fridginside-list',
  templateUrl: './fridgeInsideList.component.html',
  styleUrls: ['./fridgeInsideList.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class FridgeInsideListComponent implements OnInit, AfterViewInit, OnDestroy {
  isLoading = false;
  userIsAuthenticated = false;

  fridge: Fridge;
  floors: Floor[] = [];
  foodList: Food[] = [];

  private floorsSub: Subscription;
  private authStatusSub: Subscription;

  tabLoadTimes: Date[] = [];
  tabs = ['First', 'Second', 'Third'];

  displayedColumns: string[] = ['id', 'name', 'progress', 'color'];
  dataSource: MatTableDataSource<UserData>;

  selected = new FormControl(0);

  constructor(
    public floorService: FloorService,
    public fridgeService: FridgeService,
    private authService: AuthService) {
    // Create 100 users
    const users = Array.from({length: 100}, (_, k) => createNewUser(k + 1));

    // Assign the data to the data source for the table to render
    this.dataSource = new MatTableDataSource(users);
  }

  private paginator: MatPaginator;
  private sort: MatSort;


  @ViewChild(MatSort, {static: false}) set content(ms: MatSort) {
    this.sort = ms;
    this.setDataSourceAttributes();
    // this.dataSource.sort = ms;
  }

  @ViewChild(MatPaginator, {static: false}) set matPaginator(mp: MatPaginator) {
      this.paginator = mp;
      this.setDataSourceAttributes();
  }

  setDataSourceAttributes() {
    this.dataSource.paginator = this.paginator;
    this.dataSource.sort = this.sort;
  }

  ngOnInit() {
    this.isLoading = true;
    this.fridge = this.fridgeService.getCurrentFridge();
    this.floorService.getFloors();
    this.floorsSub = this.floorService.getFloorUpdateListener()
      .subscribe((floorData: {floors: Floor[]}) => {
        this.isLoading = false;
        this.floors = floorData.floors;
        console.log(this.floors);
      });
    this.userIsAuthenticated = this.authService.getIsAuth();
    this.authStatusSub = this.authService.getAuthStatusListener().subscribe(isAuthenticated => {
      this.userIsAuthenticated = isAuthenticated;
    });
    this.dataSource.sort = this.sort;
  }

  ngAfterViewInit() {
    this.dataSource.paginator = this.paginator;
    this.dataSource.sort = this.sort;
  }

  applyFilter(filterValue: string) {
    this.dataSource.filter = filterValue.trim().toLowerCase();

    if (this.dataSource.paginator) {
      this.dataSource.paginator.firstPage();
    }
  }

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

  ngOnDestroy() {
    this.floorsSub.unsubscribe();
  }
}

/** Builds and returns a new User. */
function createNewUser(id: number): UserData {
  const name = NAMES[Math.round(Math.random() * (NAMES.length - 1))] + ' ' +
      NAMES[Math.round(Math.random() * (NAMES.length - 1))].charAt(0) + '.';

  return {
    id: id.toString(),
    // tslint:disable-next-line:object-literal-shorthand
    name: name,
    progress: Math.round(Math.random() * 100).toString(),
    color: COLORS[Math.round(Math.random() * (COLORS.length - 1))]
  };
}
