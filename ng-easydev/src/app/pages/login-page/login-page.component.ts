import {Component} from '@angular/core';
import {FormControl, FormGroup, Validators} from "@angular/forms";
import {AuthenticationGuard} from "../../guards/authentication.guard";
import {Router} from "@angular/router";

@Component({
  templateUrl: './login-page.component.html',
  styleUrls: ['./login-page.component.css']
})
export class LoginPageComponent {

  public loginForm: FormGroup;

  constructor(protected router: Router,
              protected authGuard: AuthenticationGuard) {
    this.loginForm = new FormGroup({
      username: new FormControl('', [Validators.required]),
      password: new FormControl('', [Validators.required]),
    });
  }

  authenticate() {
    if (this.loginForm.valid) {
      const username = this.loginForm.get('username')?.value;
      const password = this.loginForm.get('password')?.value;
      this.authGuard.authenticate(username, password)
        .subscribe({
          next: () => this.router.navigate(['/home']),
        });
    }
  }
}
