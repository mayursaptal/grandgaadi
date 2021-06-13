import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { PodPage } from './pod.page';

const routes: Routes = [
  {
    path: '',
    component: PodPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class PodPageRoutingModule {

}
