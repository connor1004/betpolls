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
import OptionsHelper from '../../../helpers/OptionsHelper';

import Item from './Item';
import Add from './Add';
import Header from './Header';

class Home extends Component {
  constructor(props) {
    super(props);
    this.state = {
      leagues: [],
      list: [],
      isSearching: false
    };

    this.handleAdd = this.handleAdd.bind(this);
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.handleDragEnd = this.handleDragEnd.bind(this);
  }

  async componentDidMount() {
    const {
      body
    } = await Api.get('admin/sports/leagues/all');
    const leagues = OptionsHelper.getOptions(body, 'id', 'name');
    await this.setState({
      leagues
    });

    this.search();
  }

  async search(isSearching = true) {
    this.setState({
      isSearching
    });
    const data = await Api.get('admin/generals/options/home_leagues');
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

  async submit() {
    const {
      list
    } = this.state;
    const {
      response, body
    } = await Api.put('admin/generals/options/home_leagues', list);
    switch (response.status) {
      case 200:
        this.setState({
          list: body
        });
        break;
      default:
        break;
    }
  }

  async handleAdd(values, bags) {
    bags.setSubmitting(true);
    const {
      list
    } = this.state;
    list.push(values);
    bags.setSubmitting(false);
    await this.setState({
      list
    });
    await this.submit();
    return values;
  }

  async handleEdit(index, values, bags, component) {
    bags.setSubmitting(true);
    const {
      list
    } = this.state;
    list[index] = values;
    component.toggleEditMode();
    await bags.setSubmitting(false);
    await this.setState({
      list
    });
    await this.submit();
    return values;
  }

  async handleDelete(index, component) {
    component.setDeleting(true);
    const {
      list
    } = this.state;
    await list.splice(index, 1);
    await this.setState({
      list
    });
    await this.submit();
    component.setDeleting(false);
    return true;
  }

  async handleDragEnd({ destination, source }) {
    const {
      list
    } = this.state;
    const [removed] = list.splice(source.index, 1);
    list.splice(destination.index, 0, removed);
    this.setState({
      list
    });
  }

  render() {
    const {
      list, isSearching, leagues
    } = this.state;
    return (
      <Page
        title="Home"
        fullWidth
      >
        <Card sectioned>
          <DragDropContext onDragEnd={this.handleDragEnd}>
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
                >
                  <Header />
                  {list.map((data, index) => (
                    <Draggable draggableId={`${index}`} index={index} key={`${index}`}>
                      {(provided, snapshot) => (
                        <div
                          role="presentation"
                          className={classnames({
                            active: snapshot.isDragging
                          })}
                          ref={provided.innerRef}
                          {...provided.draggableProps}
                          {...provided.dragHandleProps}
                          style={
                            { ...provided.draggableProps.style }
                          }
                        >
                          <Item
                            index={index}
                            data={data}
                            leagues={leagues}
                            isLoading={isSearching}
                            onEdit={this.handleEdit}
                            onDelete={this.handleDelete}
                          />
                        </div>
                      )}
                    </Draggable>
                  ))}
                  {provided.placeholder}
                  <Add onAdd={this.handleAdd} leagues={leagues} />
                </div>
              )}
            </Droppable>
          </DragDropContext>
        </Card>
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

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Home));
