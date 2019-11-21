import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { FridgeService } from '../fridge.service';
import { AuthService } from 'src/app/auth/auth.service';
import { Fridge } from '../fridge.model';
import { Router } from '@angular/router';

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
  private currentFridge = null;

  constructor(
    public fridgeService: FridgeService,
    private router: Router,
    private authService: AuthService) {}

  getCurrentfridge() {
    return this.currentFridge;
  }

  ngOnInit() {
    this.isLoading = true;
    this.fridgeService.getFridges();
    this.fridgesSub = this.fridgeService.getFridgeUpdateListener()
      // fridgeData same format as getposts from post.service
      .subscribe((fridgeData: {fridges: Fridge[]}) => {
        this.isLoading = false;
        this.fridges = fridgeData.fridges;
      });
    this.userIsAuthenticated = this.authService.getIsAuth();
    this.authStatusSub = this.authService.getAuthStatusListener().subscribe(isAuthenticated => {
      this.userIsAuthenticated = isAuthenticated;
    });
  }

  setFridge(fridgeId: number) {
    this.isLoading = true;
    this.fridgeService.setCurrentFridge(fridgeId);
    this.router.navigate(['/fridge/floors']);
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
