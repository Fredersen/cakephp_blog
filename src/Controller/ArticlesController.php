<?php

namespace App\Controller;

class ArticlesController extends AppController
{
    public function index()
    {
        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    public function view($slug = null)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }

    public function add()
    {
        // Create an empty entity
        $article = $this->Articles->newEmptyEntity();
        $this->set(compact('article'));

        // Check if the request is a POST request
        if ($this->request->is('Post')) {
            // Fill the entity with the data from the request
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            $article->user_id = $this->request->getAttribute('identity')->getIdentifier();

            // Try to save the entity
            if ($this->Articles->save($article)) {
                $this->Flash->success('Your article has been saved.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Unable to add your article.');
        }
    }

    public function edit($slug)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->request->is(['Post', 'Put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                'accessibleFields' => ['user_id' => false]
            ]);
            if ($this->Articles->save($article)) {
                $this->Flash->success('Your article has been updated.');
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('Unable to update your article.');
        }

        $this->set('article', $article);
    }

    public function delete($slug)
    {
        $this->request->allowMethod(['Post', 'Delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success('The article with title: {0} has been deleted.', h($article->title));
            return $this->redirect(['action' => 'index']);
        }
    }
}
