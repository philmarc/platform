<?php

use Platform\Pages\Model\Page;

class Platform_Pages_Api_Pages_Controller extends API_Controller
{
	public function get_index($id = false)
	{
		$config = Input::get() + array(
			'where' => array(),
		);

		try
		{
			if ($id == false)
			{
				$page = Page::all(function($query) use ($config) {

					if ( ! empty($config['where']))
					{
						if (is_array($config['where'][0]))
						{
							foreach ($config['where'] as $where)
							{
								$query = $query->where($where[0], $where[1], $where[2]);
							}
						}
						else
						{
							$where = $config['where'];
							$query = $query->where($where[0], $where[1], $where[2]);
						}
					}

					return $query;

				});
			}
			else
			{
				$page = Page::find(function($query) use ($id, $config) {

					if ( ! empty($config['where']))
					{
						if (is_array($config['where'][0]))
						{
							foreach ($config['where'] as $where)
							{
								$query = $query->where($where[0], $where[1], $where[2]);
							}
						}
						else
						{
							$where = $config['where'];
							$query = $query->where($where[0], $where[1], $where[2]);
						}
					}

					$field = ( is_numeric($id)) ? 'id' : 'slug';

					return $query->where($field, '=', $id);
				});
			}

			if ($page)
			{
				return new Response($page);
			}

			return new Response(Lang::line('platform/pages::messages.pages.not_found')->get(), API::STATUS_NOT_FOUND);
		}
		catch (Exception $e)
		{
			return new Response(Lang::line('platform/pages::messages.pages.not_found')->get(), API::STATUS_NOT_FOUND);
		}
	}

	public function post_index()
	{
		// Get the Post Data
		//
		$page = new Page(Input::get());

		try
		{
			if ($page->save())
			{
				return new Response($page, API::STATUS_CREATED);
			}

			return new Response(array(
				'message' => Lang::line('platform/pages::messages.pages.create.error')->get(),
				'errors'  => ($page->validation()->errors->has()) ? $page->validation()->errors->all() : array(),
				), ($page->validation()->errors->has()) ? API::STATUS_BAD_REQUEST : API::STATUS_UNPROCESSABLE_ENTITY);
		}
		catch (Exception $e)
		{
			return new Response(array(
				'message' => $e->getMessage(),
			), API::STATUS_BAD_REQUEST);
		}
	}

	public function put_index($id)
	{
		$page = new Page(array_merge(
			array('id' => $id), Input::get()
		));

		// make sure they arne't disabling the default page
		//
		if ($page->id == Platform::get('platform/pages::default.page') and $page->status == 0)
		{
			return new Response(array(
					'message' => Lang::line('platform/pages::messages.pages.edit.error_default_page')->get(),
				), API::STATUS_BAD_REQUEST);
		}

		try
		{
			if ($page->save())
			{
				return new Response($page);
			}

			return new Response(array(
					'message' => Lang::line('platform/pages::messages.pages.edit.error')->get(),
					'errors'  => ($page->validation()->errors->has()) ? $page->validation()->errors->all() : array(),
				), ($page->validation()->errors->has()) ? API::STATUS_BAD_REQUEST : API::STATUS_UNPROCESSABLE_ENTITY);
		}
		catch (Exception $e)
		{
			return new Response(array(
				'message' => $e->getMessage(),
			), API::STATUS_BAD_REQUEST);
		}
	}

	public function delete_index($id)
	{
		try
		{
			$page = Page::find($id);

			if ($page === null)
			{
				return new Response(array(
					'message' => Lang::line('platform/pages::messages.pages.delete.error')->get()
				), API::STATUS_NOT_FOUND);
			}

			if ($page->delete())
			{
				return new Response(null, API::STATUS_NO_CONTENT);
			}

			return new Response(array(
				'message' => Lang::line('platform/pages::messages.pages.delete.error')->get(),
				'errors'  => ($page->validation()->errors->has()) ? $page->validation()->errors->all() : array(),
			), ($page->validation()->errors->has()) ? API::STATUS_BAD_REQUEST : API::STATUS_UNPROCESSABLE_ENTITY);
		}
		catch (Exception $e)
		{
			return new Response(array(
				'message' => $e->getMessage(),
			), API::STATUS_BAD_REQUEST);
		}
	}

	public function get_datatable()
	{
		$defaults = array(
			'select'    => array(
				'id'       => Lang::line('platform/pages::table.pages.id')->get(),
				'name'     => Lang::line('platform/pages::table.pages.name')->get(),
				'slug'     => Lang::line('platform/pages::table.pages.slug')->get(),
				'template' => Lang::line('platform/pages::table.pages.template')->get(),
				'status'   => Lang::line('platform/pages::table.pages.status')->get(),
			),
			'alias'     => array(),
			'where'     => array(),
			'order_by'  => array('id' => 'desc'),
		);

		// lets get to total user count
		$count_total = Page::count();

		// get the filtered count
		// we set to distinct because a user can be in multiple groups
		$count_filtered = Page::count('id', false, function($query) use ($defaults)
		{
			// sets the where clause from passed settings
			$query = Table::count($query, $defaults);

			return $query;
		});

		// set paging
		$paging = Table::prep_paging($count_filtered, 20);

		$items = Page::all(function($query) use ($defaults, $paging)
		{
			list($query, $columns) = Table::query($query, $defaults, $paging);

			return $query
				->select($columns);

		});

		$items = ($items) ?: array();

		return new Response(array(
			'columns'        => $defaults['select'],
			'rows'           => $items,
			'count'          => $count_total,
			'count_filtered' => $count_filtered,
			'paging'         => $paging,
		));
	}
}