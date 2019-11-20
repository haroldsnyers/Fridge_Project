import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';


import { LoginComponent } from './auth/login/login.component';
import { HomeComponent } from './home/home.component';
import { SignupComponent } from './auth/signup/signup.component';
import { FridgeCreateComponent } from './fridge/fridge-create/fridge-create.component';
import { FridgeListComponent } from './fridge/fridge-list/fridge-list.component';
import { FridgeInsideListComponent } from './fridgeInside/fridgeInsideList/fridgeInsideList.component';


const routes: Routes = [
  { path: '', component: HomeComponent},
  { path: 'login', component: LoginComponent},
  { path: 'signup', component: SignupComponent},
  { path: 'fridge/create', component: FridgeCreateComponent},
  { path: 'fridge/edit/:fridgeId', component: FridgeCreateComponent},
  { path: 'fridges', component: FridgeListComponent},
  { path: 'fridge/floors', component: FridgeInsideListComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
