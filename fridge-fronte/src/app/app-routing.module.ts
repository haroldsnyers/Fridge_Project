import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';


import { LoginComponent } from './auth/login/login.component';
import { HomeComponent } from './home/home.component';
import { SignupComponent } from './auth/signup/signup.component';
import { FridgeCreateComponent } from './fridge/fridge-create/fridge-create.component';
import { FridgeListComponent } from './fridge/fridge-list/fridge-list.component';
import { FridgeInsideListComponent } from './fridgeInside/fridgeInsideList/fridgeInsideList.component';
import { AuthGuard } from './auth/auth.guard';


const routes: Routes = [
  { path: '', component: HomeComponent},
  { path: 'login', component: LoginComponent},
  { path: 'signup', component: SignupComponent},
  { path: 'fridge/create', component: FridgeCreateComponent, canActivate: [AuthGuard]},
  { path: 'fridge/edit/:fridgeId', component: FridgeCreateComponent, canActivate: [AuthGuard]},
  { path: 'fridges', component: FridgeListComponent, canActivate: [AuthGuard]},
  { path: 'fridge/floors', component: FridgeInsideListComponent, canActivate: [AuthGuard]}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
  providers: [AuthGuard]
})
export class AppRoutingModule { }
