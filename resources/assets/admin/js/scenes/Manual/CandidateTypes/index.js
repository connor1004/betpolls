/* eslint-disable no-shadow */
import React, { Component } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import classnames from 'classnames';
import {
  Page, Card
} from '@shopify/polaris';

import Api from '../../../apis/app';
import Action from './Action';
import Item from './Item';
import Add from './Add';
import Header from './Header';

class CandidateTypes extends Component {
  constructor(props) {
    super(props);
    this.state = {
      list: [],
      search: {},
      isSearching: false
    };

    this.handleSearch = this.handleSearch.bind(this);
    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleDragEnd = this.handleDragEnd.bind(this);
  }

  componentDidMount() {
    this.search();
  }

  async search(isSearching = true) {
    const { search } = this.state;
    this.setState({
      isSearching
    });
    const data = await Api.get('admin/manual/candidate-types', search);
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
    await this.setState({
      search
    });
    this.search();
  }

  async handleAdd(values, bags) {
    bags.setSubmitting(true);
    const data = await Api.post('admin/manual/candidate-types', values);
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
    const data = await Api.put(`admin/manual/candidate-types/${id}`, values);
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
    const data = await Api.put(`admin/manual/candidate-types/${id}/toggle-active`);
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
    const data = await Api.delete(`admin/manual/candidate-types/${id}`);
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
      list
    } = this.state;
    const sourceItem = list[source.index];
    const destinationItem = list[destination.index];
    const [removed] = list.splice(source.index, 1);
    list.splice(destination.index, 0, removed);

    await Api.put(`admin/manual/candidate-types/${sourceItem.display_order}/${destinationItem.display_order}`);
    this.search(false);
  }

  render() {
    const {
      list, isSearching
    } = this.state;
    return (
      <Page
        title="Candidate Types"
        fullWidth
      >
        <Card sectioned>
          <Action
            onSearch={this.handleSearch}
          />
        </Card>
        <Card sectioned>
          {/* <DragDropContext onDragEnd={this.handleDragEnd}>
            <Droppable droppableId="categories">
              {(provided, snapshot) => (
                <div
                  ref={provided.innerRef}
                  className={
                    classnames({
                      categories: true,
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
                    //   )}
                    // </Draggable>
                  ))}
                  {/* {provided.placeholder}
                </div>
              )}
            </Droppable>
          </DragDropContext> */}
        </Card>
        <Add onAdd={this.handleAdd} />
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

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(CandidateTypes));
