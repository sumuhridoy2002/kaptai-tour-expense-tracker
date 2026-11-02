<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;

class TourController
{
 public function index() {
  $members = DB::table('members')->orderByDesc('id')->get();
  $categories = DB::table('categories')->leftJoin('expenses','categories.id','=','expenses.category_id')
   ->select('categories.id','categories.name')->selectRaw('COALESCE(SUM(expenses.amount_paisa),0) as total_paisa')
   ->groupBy('categories.id','categories.name')->orderBy('categories.name')->get();
  $expenses = DB::table('expenses')->join('categories','categories.id','=','expenses.category_id')
   ->select('expenses.*','categories.name as category')->orderByDesc('spent_on')->orderByDesc('expenses.id')->get();
  $deposited = (int)$members->sum('amount_paisa');
  $spent = (int)$expenses->sum('amount_paisa');
  return view('tour', compact('members','categories','expenses','deposited','spent'));
 }
 private function paisa(string $value): int {
  [$whole,$fraction] = array_pad(explode('.', $value, 2), 2, '');
  return ((int)$whole * 100) + (int)str_pad($fraction,2,'0');
 }
 private function moneyRules(): array {
  return ['required','regex:/^(?:0|[1-9][0-9]{0,7})(?:\\.[0-9]{1,2})?$/','numeric','min:0.01','max:99999999.99'];
 }
 private function insertOnce(string $table, array $data) {
  try { DB::table($table)->insert($data + ['created_at'=>now(),'updated_at'=>now()]); }
  catch (UniqueConstraintViolationException $e) {
   if (!DB::table($table)->where('submission_id',$data['submission_id'])->exists()) { throw $e; }
   return redirect()->route('tour')->with('success','This entry was already saved.');
  }
  return redirect()->route('tour')->with('success','Entry saved successfully.');
 }
 public function member(Request $r) {
  $r->merge(['name'=>trim((string)$r->input('name'))]);
  $d=$r->validateWithBag('member',['name'=>'required|string|max:120','amount'=>$this->moneyRules(),'submission_id'=>'required|uuid']);
  return $this->insertOnce('members',['name'=>$d['name'],'amount_paisa'=>$this->paisa($d['amount']),'submission_id'=>$d['submission_id']]);
 }
 public function category(Request $r) {
  $r->merge(['name'=>preg_replace('/\\s+/u',' ',trim((string)$r->input('name')))]);
  $d=$r->validateWithBag('category',['name'=>'required|string|max:80|unique:categories,name']);
  try { DB::table('categories')->insert($d+['created_at'=>now(),'updated_at'=>now()]); }
  catch (UniqueConstraintViolationException $e) { return redirect()->route('tour')->withErrors(['name'=>'This category already exists.'],'category')->withInput(); }
  return redirect()->route('tour')->with('success','Category added.');
 }
 public function expense(Request $r) {
  $r->merge(['title'=>trim((string)$r->input('title'))]);
  $d=$r->validateWithBag('expense',['title'=>'required|string|max:180','category_id'=>'required|integer|exists:categories,id','amount'=>$this->moneyRules(),'spent_on'=>'required|date_format:Y-m-d','submission_id'=>'required|uuid']);
  return $this->insertOnce('expenses',['title'=>$d['title'],'category_id'=>$d['category_id'],'amount_paisa'=>$this->paisa($d['amount']),'spent_on'=>$d['spent_on'],'submission_id'=>$d['submission_id']]);
 }
}
