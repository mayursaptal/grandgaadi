import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-tab3',
  templateUrl: 'tab3.page.html',
  styleUrls: ['tab3.page.scss']
})
export class Tab3Page {

  public displayName: any;
  public userEmail: any;

  constructor(private router: Router) {
    const userdata = JSON.parse(localStorage.getItem('userdata'));
    this.displayName  = userdata.display_name;
    this.userEmail    = userdata.user_email;

  }
  logout() {
    localStorage.clear();
    this.router.navigateByUrl('login');
  }
}
