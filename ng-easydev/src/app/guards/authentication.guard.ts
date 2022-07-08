import {ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot} from "@angular/router";
import {Observable, of} from "rxjs";
import {Injectable} from "@angular/core";

@Injectable({
  providedIn: 'root',
})
export class AuthenticationGuard implements CanActivate {

  private authenticationToken: string | undefined;

  constructor(private router: Router) {
  }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> {
    if (!this.authenticationToken) {
      this.router.navigate(['/login']);
    }
    return of(!!this.authenticationToken);
  }

  public authenticate(user: string, password: string): Observable<boolean> {
    // TODO call a real authentication method
    this.authenticationToken = user;
    return of(true);
  }
}
