import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {AuthenticationGuard} from "./guards/authentication.guard";
import {LoginPageComponent} from "./pages/login-page/login-page.component";
import {HomePageComponent} from "./pages/home-page/home-page.component";

const routes: Routes = [
  {path: 'login', component: LoginPageComponent},
  {
    path: '',
    canActivate: [AuthenticationGuard],
    children: [
      {path: 'home', component: HomePageComponent},
    ],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
