import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { Scanner2Page } from './scanner2.page';

const routes: Routes = [
  {
    path: '',
    component: Scanner2Page
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class Scanner2PageRoutingModule {}
