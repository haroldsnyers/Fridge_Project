import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';


import { LoginComponent } from './auth/login/login.component';
import { HomeComponent } from './home/home.component';
import { SignupComponent } from './auth/signup/signup.component';
import { FridgeCreateComponent } from './fridge/fridge-create/fridge-create.component';
import { FridgeListComponent } from './fridge/fridge-list/fridge-list.component';
import { FridgeInsideListComponent } from './fridgeInside/fridgeInsideList/fridgeInsideList.component';
import { AuthGuard } from './auth/auth.guard';
import { FloorCreateComponent } from './fridgeInside/floor-create/floor-create.component';
import { FoodCreateComponent } from './fridgeInside/food-create/food-create.component';


const routes: Routes = [
  { path: '', component: HomeComponent},
  { path: 'login', component: LoginComponent},
  { path: 'signup', component: SignupComponent},
  { path: 'fridge/create', component: FridgeCreateComponent, canActivate: [AuthGuard]},
  { path: 'fridge/edit/:fridgeId', component: FridgeCreateComponent, canActivate: [AuthGuard]},
  { path: 'fridges', component: FridgeListComponent, canActivate: [AuthGuard]},
  { path: 'fridge/floors', component: FridgeInsideListComponent, canActivate: [AuthGuard]},
  { path: 'fridge/floor/create', component: FloorCreateComponent, canActivate: [AuthGuard]},
  { path: 'fridge/floor/edit/:floorId', component: FloorCreateComponent, canActivate: [AuthGuard]},
  { path: 'fridge/food/create', component: FoodCreateComponent, canActivate: [AuthGuard]},
  { path: 'fridge/food/edit/:foodId', component: FoodCreateComponent, canActivate: [AuthGuard]},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
  providers: [AuthGuard]
})
export class AppRoutingModule { }
