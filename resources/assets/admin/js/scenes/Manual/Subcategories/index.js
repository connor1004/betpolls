/* eslint-disable no-shadow */
import React, { Component } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import classnames from 'classnames';
import QueryString from 'qs';
import {
  Page, Card
} from '@shopify/polaris';

import Api from '../../../apis/app';
import Action from './Action';
import Item from './Item';
import Add from './Add';
import Header from './Header';
import OptionsHelper from '../../../helpers/OptionsHelper';

class Subcategories extends Component {
  constructor(props) {
    super(props);
    this.state = {
      categories: [],
      list: [],
      search: {
        inactive: false
      },
      isSearching: false
    };

    this.handleSearch = this.handleSearch.bind(this);
    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleDragEnd = this.handleDragEnd.bind(this);
  }

  async componentDidMount() {
    const categoriesRes = await Api.get('admin/manual/categories');
    const { body: categories } = categoriesRes;
    const search = QueryString.parse(this.props.location.search, { ignoreQueryPrefix: true });
    search.inactive = search.inactive === 'true';
    if (categories.length > 0) {
      if (!search.category_id) {
        search.category_id = categories[0].id;
      }
    }
    await this.setState({
      categories: OptionsHelper.getOptions(categories, 'id', 'name'),
      search
    });
    this.search();
  }

  async componentWillReceiveProps(props) {
    if (props.location.search !== this.props.location.search) {
      const search = QueryString.parse(props.location.search, { ignoreQueryPrefix: true });
      search.inactive = search.inactive === 'true';
      const { categories } = this.state;
      if (categories.length > 0) {
        if (!search.category_id) {
          search.category_id = categories[0].id;
        }
      }
      await this.setState({
        search
      }, () => {
        this.search();
      });
    }
  }

  async search(isSearching = true) {
    const { search } = this.state;
    this.setState({
      isSearching
    });
    const data = await Api.get('admin/manual/subcategories', search);
    const {
      response, body
    } = data;

    switch (response.status) {
      case 200:
        this.setState({
          list: body
        });
        break;
      default:
        break;
    }
    this.setState({
      isSearching: false
    });
  }

  async handleSearch(search) {
    const url = `/admin/manual/subcategories${QueryString.stringify(search, { addQueryPrefix: true })}`;
    this.props.history.push(url);
  }

  async handleAdd(values, bags) {
    bags.setSubmitting(true);
    const {
      search
    } = this.state;
    // eslint-disable-next-line no-param-reassign
    values.category_id = search.category_id;
    const data = await Api.post('admin/manual/subcategories', values);
    const { response, body } = data;
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 406:
        bags.setStatus(body.error);
        break;
      case 200:
        bags.setStatus(null);
        bags.setValues({
          logo: '',
          name: '',
          name_es: '',
          slug: '',
          slug_es: '',
          meta_keywords: '',
          meta_keywords_es: '',
          meta_description: '',
          meta_description_es: '',
          content: '',
          content_es: ''
        });
        bags.setTouched({
          logo: false,
          name: false,
          name_es: false,
          slug: false,
          slug_es: false,
          meta_keywords: false,
          meta_keywords_es: false,
          meta_description: false,
          meta_description_es: false,
          content: false,
          content_es: false
        });
        this.search();
        break;
      default:
        break;
    }
    bags.setSubmitting(false);
    return data;
  }

  async handleEdit(id, values, bags, component) {
    bags.setSubmitting(true);
    const data = await Api.put(`admin/manual/subcategories/${id}`, values);
    const { response, body } = data;
    switch (response.status) {
      case 422:
        bags.setErrors(body);
        break;
      case 406:
        bags.setStatus(body.error);
        break;
      case 200:
        bags.setStatus(null);
        this.search();
        break;
      default:
        break;
    }
    component.toggleEditMode();
    bags.setSubmitting(false);
    return data;
  }

  async handleToggleActive(id) {
    const data = await Api.put(`admin/manual/subcategories/${id}/toggle-active`);
    const { response } = data;
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200:
        this.search();
        break;
      default:
        break;
    }
    return data;
  }

  async handleDelete(id, component) {
    component.setDeleting(true);
    const data = await Api.delete(`admin/manual/subcategories/${id}`);
    const { response } = data;
    switch (response.status) {
      case 422:
        break;
      case 406:
        break;
      case 200:
        this.search();
        break;
      default:
        break;
    }
    component.setDeleting(false);
    return data;
  }

  async handleDragEnd({ destination, source }) {
    const {
      list, search
    } = this.state;
    const sourceItem = list[source.index];
    const destinationItem = list[destination.index];
    const [removed] = list.splice(source.index, 1);
    list.splice(destination.index, 0, removed);

    await Api.put(`admin/manual/subcategories/${search.category_id}/${sourceItem.display_order}/${destinationItem.display_order}`);
    this.search(false);
  }

  render() {
    const {
      list, isSearching, categories, areas, search
    } = this.state;
    return (
      <Page
        title="Subcategories"
        fullWidth
      >
        <Card sectioned>
          <Action
            search={search}
            categories={categories}
            onSearch={this.handleSearch}
          />
        </Card>
        <Card sectioned>
          {/* <DragDropContext onDragEnd={this.handleDragEnd}>
            <Droppable droppableId="areas">
              {(provided, snapshot) => (
                <div
                  ref={provided.innerRef}
                  className={
                    classnames({
                      areas: true,
                      active: snapshot.isDraggingOver
                    })
                  }
                > */}
                  <Header />
                  {list.map((data, index) => (
                    // <Draggable draggableId={data.id} index={index} key={`${index}`}>
                    //   {(provided, snapshot) => (
                        <div key={`${index}`}
                          // role="presentation"
                          // className={classnames({
                          //   active: snapshot.isDragging
                          // })}
                          // ref={provided.innerRef}
                          // {...provided.draggableProps}
                          // {...provided.dragHandleProps}
                          // style={
                          //   { ...provided.draggableProps.style }
                          // }
                        >
                          <Item
                            data={data}
                            isLoading={isSearching}
                            onEdit={this.handleEdit}
                            onDelete={this.handleDelete}
                            onToggleActive={this.handleToggleActive}
                          />
                        </div>
                      // )}
                    // </Draggable>
                  ))}
                  {/* {provided.placeholder} */}
                {/* </div> */}
              {/* )} */}
            {/* </Droppable>
          </DragDropContext> */}
        </Card>
        <Add
          onAdd={this.handleAdd}
        />
      </Page>
    );
  }
}


const mapStateToProps = state => ({
  auth: state.common.auth
});

const mapDispatchToProps = () /* dispatch */ => ({
  // logout: bindActionCreators(logout, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Subcategories));
