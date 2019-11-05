import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { FridgeService } from '../fridge.service';
import { AuthService } from 'src/app/auth/auth.service';
import { Fridge } from '../fridge.model';

@Component({
  selector: 'app-fridge-list',
  templateUrl: './fridge-list.component.html',
  styleUrls: ['./fridge-list.component.css']
})
export class FridgeListComponent implements OnInit, OnDestroy {
  fridges: Fridge[] = [];
  isLoading = false;
  userIsAuthenticated = false;
  private fridgesSub: Subscription;
  private authStatusSub: Subscription;

  private data: JSON[];

  constructor(public fridgeService: FridgeService, private authService: AuthService) {}

  ngOnInit() {
    this.isLoading = true;
    this.fridgeService.getFridges();
    this.fridgesSub = this.fridgeService.getFridgeUpdateListener()
      // fridgeData same format as getposts from post.service
      .subscribe((fridgeData: {fridges: Fridge[]}) => {
        console.log(fridgeData);
        this.isLoading = false;
        this.fridges = fridgeData.fridges;
      });
    this.userIsAuthenticated = this.authService.getIsAuth();
    this.authStatusSub = this.authService.getAuthStatusListener().subscribe(isAuthenticated => {
      this.userIsAuthenticated = isAuthenticated;
    });
  }

  onDelete(fridgeId: string) {
    this.isLoading = true;
    this.fridgeService.deleteFridge(fridgeId).subscribe(() => {
      this.fridgeService.getFridges();
    });
  }

  ngOnDestroy() {
    this.fridgesSub.unsubscribe();
  }


}
