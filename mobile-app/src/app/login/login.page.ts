import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../Service/api.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {
  username: any;
  password: any;
  constructor(public api: ApiService, private router: Router) { }

  ngOnInit() {
  }
  login() {
    const param = {
      username: this.username,
      password: this.password
    };
    localStorage.clear();
    this.api.post('authorize', param).subscribe((data: any) => {
      this.api.loaderhide();
      if (data && data.result === 'success') {
        this.api.apikey = data.data.api_key;
        localStorage.setItem('apikey', data.data.api_key);
        localStorage.setItem('userdata', JSON.stringify(data.data));
        this.router.navigateByUrl('tabs');
        this.api.toastMsg('Login Successfull');
      } else {
        this.api.toastMsg(data.data.message);
      }
    }, error => {
      this.api.loaderhide();
      this.api.toastMsg(error.message);
    });
  }
}
